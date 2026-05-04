 <?php
require_once(DIR_SYSTEM.'library/operations/orders/order_payment.php');
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/bank_transfer.php');
/**
 * 	TentativePaymentRefund
 *  @info: This TentativePaymentRefund is class to get tentative refunds for customer
 * 	@author @Nishu, Dec 2017
 */
class TentativePaymentRefund
{
	public $registry;
	public $config;
	public $load;
	public $db;
	private $all_tentative_refunds    = array();
	private $all_error_refunds        = array();
	private $_pending_approval_orders = '';
	private $_not_applicable_orders   = '';

	private $_excess_payment_min_limit = 5;
    
    
	public function __construct($registry) {
        $this->registry = $registry;
		if (method_exists($registry, 'get')) {
            $this->db 		= $registry->get('db');
            $this->load 	= $registry->get('load');
            $this->config   = $registry->get('config');
        } else {
            $this->db 		= $registry->db;
            $this->load 	= $registry->load;
            $this->config   = $registry->config;
        }
	}

	/**
     * Public method to mark credit note's payment_cleared field as NOT_APPLICABLE
     *      If net_refundable is <= 5 or payment cleared marked in trxn_detail table 
     * @author: Nishu, Aug 2018
	*/
	public function markCreditNoteAsNotApplicable(){
		$sql = "
                SELECT 
				    cn.credit_note_id as cn_id,
				    cn.net_refundable, 
				    cn.payment_cleared,
				    td.trxn_done,
				    IFNULL(SUM(IF(td.trxn_done IN ('NOT_APPLICABLE','BANK_REQUESTED','BANK_PROCESSED','BANK_SUCCESS'), 
			                      IF(td.trxn_amount IS NULL, 0, td.trxn_amount), 
			                      0
			                     )
			                  ), 0) as ttl_trxn_amount, 
				    TRIM(LOWER(oo.payment_code)) as payment_code, 
					oo.currency_code,
					oo.stock_transfer,
					oo.franchise_id
				FROM
				    ".DB_PREFIX."credit_note as cn
				    	INNER JOIN
				    ".DB_PREFIX."order as oo ON oo.order_id = cn.order_id
				        INNER JOIN 
				    ".DB_PREFIX."suborder osub ON osub.order_id = cn.order_id 
				    	LEFT JOIN
				    ".DB_PREFIX."trxn_details as td ON td.trxn_for_id = cn.credit_note_id AND td.trxn_for = 'CREDIT_NOTE'
				WHERE cn.payment_cleared = 'NO' 
				    AND cn.suborder_id = osub.suborder_id 
				GROUP BY cn.credit_note_id
				HAVING  
				    ((net_refundable - ttl_trxn_amount) <= 5 AND (net_refundable - ttl_trxn_amount) >= 0) 
				    OR oo.stock_transfer = 1 
				    OR oo.franchise_id > 0 
					OR payment_code IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "') 
					OR oo.currency_code != 'INR' 
		       ";
		      
		 $result = $this->db->query($sql);
		 if($result->num_rows > 0){

		 	foreach ($result->rows as $key => $row) {
		 		//To update trxn_details and oc-credit_note table for NOT_APPLICABLE
		 		$this->updateTrxnAndCnForNotApplicable($row);
		 	
		 	}//End of Foreach
		 }//End of If
	}

	/**
     * Private method to update trxn_details and oc-credit_note table for NOT_APPLICABLE
     * @param: $row Array
     * @author: Nishu, Aug 2018
	*/
	private function updateTrxnAndCnForNotApplicable(array $row){
		if(!empty($row)){
			
			// calculating refunds
	        $ttl_trxn_amount          = $row['ttl_trxn_amount'] ?? 0; // total refund paid already for this CN
	        $actual_refundable_amount = $row['net_refundable']  ?? 0; // total refundable amount for this CN

	        $balance_refund_amount    = $actual_refundable_amount - $ttl_trxn_amount;

		 	//Refund for this CN is not applicable
	    	$trxn_data = array();
	        $trxn_data['trxn_for']        = 'CREDIT_NOTE';
	        $trxn_data['trxn_for_id']     = (int)$row['cn_id'];
	        //To check already existance of same $cn_id in oc_trxn_details with following trxn_status
	        $trxn_data['trxn_done']       = 'NOT_DONE'; 
	        $trxn_data['trxn_amount']     = $balance_refund_amount;
	        $trxn_data['trxn_date_added'] = 'Now()';
	        //Mark trxn_details as NOT_APPLICABLE in oc_trxn_details table
	    	$this->markCnRefundNotApplicable($trxn_data);

			//Mark payement_cleared field as NOT_APPLICABLE in oc_credit_note table
	    	$cn_data = array();
	    	$cn_data['credit_note_id']  = (int)$row['cn_id'];
	    	$cn_data['payment_cleared'] = 'NOT_APPLICABLE';

	    	$cn_obj = new CreditNote($this->registry);
	    	$cn_obj->updateCnPaymentClearedFlag($cn_data);
		}
	}


	/**
	 * @info: Public method to cancel cashback / coupon (cash discounts), 
	 * for FULLY Cancelled Orders. They should not be refunded in case of complete cancellation.
	 * @author: Nishu, 26th Feb 2018
	*/
	public function cancelCashbackCouponInFullyCanceledOrders(){
		// Find orders where there is cashback/coupon and all suborders have been cancelled.
		$sql = "
				SELECT 
				    GROUP_CONCAT(DISTINCT so.order_status_id) AS suborder_statuses, 
				    op.payment_id
				FROM
				    ".DB_PREFIX."order_payment AS op 
			    INNER JOIN
				    ".DB_PREFIX."suborder AS so ON so.order_id = op.order_id
				WHERE op.successfull = 1
				  AND op.payment_gateway IN ('cashback' , 'coupon')
				GROUP BY op.payment_id
				HAVING suborder_statuses = '2'
			   ";
		$result = $this->db->query($sql);

		if($result->num_rows > 0){
			$payment_ids = array_column($result->rows, 'payment_id');
			$payment_ids = implode(',', $payment_ids);
			$sql = "UPDATE 
					  ".DB_PREFIX."order_payment
					SET 
					  successfull = 0,
					  txn_status = 'CANCELED ORDER - INVALID',  
					  reference = CONCAT('Auto-removed on ', NOW(), ' during Tentative Refund calculation, because of full order cancellation. ', reference)  
					WHERE 
					  payment_id IN (". $payment_ids .")
				   ";
			$this->db->query($sql);
		}
	}

	/**
	 * @info: Public method to cancel cashback / coupon (cash discounts), 
	 * for Partially Cancelled Orders. They should not be refunded in case of cancellation.
	 * @author: Nishu, 26th Oct 2018
	*/
	public function cancelCashbackCouponInPartiallyCanceledOrders(){

		$alert_error_array = array();

		// Find orders where there is cashback/coupon and suborders have been partially cancelled.
		$sql = "
				SELECT
				  op.order_id, 
				  op.payment_id,
				  op.reference,
				  SUM(av.value) AS advance_val,
				  op.amount AS order_payment, 
				  ROUND(ABS(op.amount - SUM(av.value)), 2) AS amt_difference 
				FROM
				  ". DB_PREFIX ."order_payment AS op
				INNER JOIN
				  ". DB_PREFIX ."advance_voucher AS av ON av.payment_id = op.payment_id
				INNER JOIN
				(
					SELECT 
					    o.order_id
					FROM
					    ".DB_PREFIX."order o
					        JOIN
					    ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
					        AND osub.order_status_id > 0
					WHERE
					    o.payment_cleared = 'NO'
					        AND o.currency_code = 'INR'
					        AND o.stock_transfer = 0
					        AND o.franchise_id = 0 
					        AND TRIM(LOWER(o.payment_code)) NOT IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "')
					        AND osub.order_status_id > 0
					GROUP BY o.order_id
					HAVING NOT SUM(osub.order_status_id <> 2
					    AND COALESCE(osub.buyer_invoice_id, 0) = 0)
				) dt ON dt.order_id = op.order_id 
				WHERE
				  op.successfull = 1 
				  AND op.payment_gateway IN('cashback', 'coupon') 
				  AND av.status = 1
				GROUP BY
				  op.payment_id 
				HAVING 
				  amt_difference >= 1
			   ";

		$result = $this->db->query($sql);
		
		if($result->num_rows > 0){
			foreach ($result->rows as $row) {
				
				$row['amt_diff'] = ROUND( ($row['order_payment'] -$row['advance_val']), 2);

				if($row['amt_diff'] >= 1){
					$this->adjustPartiallyCanceledOrderPaymentAmount($row);
				}else{
					$alert_error_array[] = $row;
				}
			}

			//Check if there is any mismatch value 
			if(!empty($alert_error_array)){
				$this->sendMailAdvanceVoucherAmountMismatch($alert_error_array);
			}
		}
	}

	/**
     * Private method to adjust amount for Partially cancelled order's applied Cashback/Coupns
     * @param: Array
     * @author: Nishu, 26th oct 2018
	*/
	private function adjustPartiallyCanceledOrderPaymentAmount(array $row){
		if(!empty($row) && !empty($row['payment_id']) ){
			
			if((float)$row['advance_val'] > 0){
				$sql = "
						SELECT * 
						FROM ". DB_PREFIX. "order_payment 
						WHERE payment_id = ". (int)$row['payment_id'];
				$result = $this->db->query($sql);
				$order_payment_data = $result->row;

				//Update already existing payment entry
			    $update_sql = "
			    				UPDATE 
			    					". DB_PREFIX ."order_payment
			    				SET
			    					amount = ". (float)$row['advance_val']."
			    				WHERE
			    					payment_id = ". (int)$row['payment_id'] ."
			    			  ";
				$this->db->query($update_sql);

				//Insert New entry for order_payment, for which refund will not be done

				//Unset primary key data for new entry
				unset( $order_payment_data['payment_id'] );

				$order_payment_data['amount']      = abs($row['amt_diff']);
				$order_payment_data['successfull'] = 0;
				$order_payment_data['txn_status']  = 'PARTIAL_CANCELED';
				$order_payment_data['reference']   = "Cashback/Coupon is cancelled due to Order partially cancelled. Deducted during tentative refund calculation.";

				$insert_sql = "
								INSERT INTO
								    ". DB_PREFIX ."order_payment 
								(`". implode( "` , `", array_keys($order_payment_data) ).
						        "`)  
						        VALUES 
						        ( '".
						          implode("' , '", $order_payment_data)
						        ."' )";

			    $this->db->query($insert_sql);
			}else{
				$reference = $row['reference'];
				//Update already existing payment entry to successfull=0
			    $update_sql = "
			    				UPDATE 
			    					". DB_PREFIX ."order_payment
			    				SET
			    					successfull = 0,
			    					txn_status  = 'PARTIAL_CANCELED',
			    					reference   = '".$this->db->escape($reference." Cashback/Coupon is cancelled due to Order partially cancelled. Deducted during tentative refund calculation.")."'
			    				WHERE
			    					payment_id = ". (int)$row['payment_id'] ."
			    			  ";
				$this->db->query($update_sql);
			}

		}
	}

	/**
     * Private method to send alert message
     * @param: Array
     * @author: Nishu, 26th oct 2018
	*/
	private function sendMailAdvanceVoucherAmountMismatch(array $row){
		if(!empty($row)){
			$html = MailTemplate::sendMailAdvanceVoucherAmountMismatch($row);
        
	        $mail = new PHPMailer();
	        $mail->isSMTP();
	        $mail->Host = $this->config->get('config_mail_smtp_hostname');
	        $mail->Port = $this->config->get('config_mail_smtp_port');
	        $mail->SMTPSecure = 'ssl';
	        $mail->SMTPAuth = true;
	        $mail->Username = $this->config->get('config_mail_smtp_username');
	        $mail->Password = $this->config->get('config_mail_smtp_password');

	        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
	        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
	        $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
	        $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
	        $mail->addCC(EMAIL_IDS['nishu']['email_id'], EMAIL_IDS['nishu']['name']);
	        $mail->Subject = 'Advance Voucher Amount exceeded from Order Payment  - ' . date('d/M/Y H:i:s', time());
	        $mail->msgHTML($html);
	        
	        $mail->send();
		}
	}

	/**
	 * Public function to intiate refund and excess payemnts against any order
	 * @param: void
	 * @return: array
	 * @author: Nishu, Oct 2017
	 */
	public function executeRefund() {

		//Mark cN's payment_cleared field as NOT_APPLICABLE, if CreditNote's amount is not refundable
		$this->markCreditNoteAsNotApplicable();

		//Refund all excess payments 
		$this->getOrdersForExcessPayment();

		//Refund all CreditNote Generated payments 
		$this->getOrdersForCreditNoteRefund();

		//update order excess payment clear with NOT_APPLICABLE
		if($this->_not_applicable_orders != ''){
		  $order_ids = trim($this->_not_applicable_orders, ',');
		  OrderEdit::updateExcessPayment($this->db, $order_ids, 'NOT_APPLICABLE');
		}
        
		//update order excess payment clear with PENDING_APPROVAL
		if($this->_pending_approval_orders != ''){
		  $order_ids = trim($this->_pending_approval_orders, ',');
		  OrderEdit::updateExcessPayment($this->db, $order_ids, 'PENDING_APPROVAL');
		}

		if(!empty($this->all_tentative_refunds)){
			$this->sendMailTentativeRefundApproval($this->all_tentative_refunds);
		}

		if(!empty($this->all_error_refunds)){
			$this->sendMailErrorRefund($this->all_error_refunds);
		}
		
		die("Completed");
	}

	/**
	 * Public function to get orders for excess payment
	 * @param: void
	 * @return: array
	 * @author: Nishu, Sept 2017
	 */
	public function getOrdersForExcessPayment() {

		// We mark all invalid orders to be NOT_APPLICABLE first
		$mark_not_applicable_sql = "UPDATE ".DB_PREFIX."order 
		                            SET payment_cleared = 'NOT_APPLICABLE' 
		                            WHERE 
		                              (currency_code != 'INR'
				                       OR store_id NOT IN (". WSB_STORES_ID .") 
				                       OR stock_transfer = 1 
				                       OR franchise_id > 0 
				                       OR TRIM(LOWER(payment_code)) IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "')
				                      ) 
				                    AND payment_cleared NOT IN ('YES', 'PENDING_APPROVAL', 'NOT_APPLICABLE')
				                    ";
		$this->db->query($mark_not_applicable_sql);
		
        // SQL
		$sql = "SELECT 
				    o.order_id,
				    o.order_no,
				    o.customer_id, 
                    TRIM(CONCAT(o.firstname, ' ', o.lastname)) as customer_name, 
				    o.email,
				    o.date_added as order_date,
				    o.payment_code,
				    o.telephone as mobile,
				    osub.suborder_id,
				    CONCAT(o.shipping_city, ', ', o.shipping_country) AS shipping_city,
				    GROUP_CONCAT(DISTINCT osub.suborder_id) AS suborder_ids,
				    GROUP_CONCAT(DISTINCT IF(osub.order_status_id = 2,
				            osub.suborder_id,
				            null)) AS cancelled_suborder_ids,
				    GROUP_CONCAT(DISTINCT IF(osub.order_status_id = 2,1,
				                             IF(osub.invoice_no > 0 AND osub.buyer_invoice_id > 0, 1, 0))
				                ) as suborderwise_flag,
				    IFNULL(SUM(IF(td.trxn_done IN ('NOT_APPLICABLE','BANK_REQUESTED','BANK_PROCESSED','BANK_SUCCESS'), 
				                  IF(td.trxn_amount IS NULL, 0, td.trxn_amount), 
				                  0
				                 )
				              ), 0) AS ttl_trxn_amount
				FROM
				    ".DB_PREFIX."order o
				    	INNER JOIN
				    ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
				    	LEFT JOIN
    				".DB_PREFIX."trxn_details as td ON td.trxn_for_id = o.order_id
        				AND td.trxn_for = 'EXCESS_PAYMENT_BY_CUSTOMER'
				WHERE
				    o.payment_cleared = 'NO'
				        AND o.currency_code = 'INR'
				        AND o.store_id IN (". WSB_STORES_ID .") 
				        AND o.stock_transfer = 0 
				        AND o.franchise_id = 0 
				        AND TRIM(LOWER(o.payment_code)) NOT IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "')
				        AND osub.order_status_id > 0 
				GROUP BY o.order_id
				HAVING suborderwise_flag = '1'
				ORDER BY o.order_id ASC
				";
		$result = $this->db->query($sql);
        // Initializing result array
		$data = array();

		if($result->num_rows > 0 ){
			
            foreach ($result->rows as $row) {
                
                // Fetching order id, order no, suborder ids etc
                $order_id 	= (int)$row['order_id'];
				$order_no	= $row['order_no'];
                
                $suborder_ids 			= explode(',', $row['suborder_ids']);
				$cancelled_suborder_ids = explode(',', $row['cancelled_suborder_ids']);
                
                // Initializing temp array 
                $temp_data = array();
                $temp_data[$order_id]['order_id'] 			= $order_id;
                $temp_data[$order_id]['order_no'] 			= $order_no;
                $temp_data[$order_id]['refund_type']        = "EXCESS_PAYMENT_BY_CUSTOMER";
                $temp_data[$order_id]['invoice_amount']     = 0;
                
                $total_payment = 0;
				$total_invoice = 0;
				
                // Getting net payment received for this order 
				$payment_sql = "SELECT 
								     opay.order_id, SUM(opay.amount) AS total_payment
								FROM
								    ".DB_PREFIX."order_payment as opay
								WHERE
								    opay.order_id = ". (int)$order_id ."
								    AND opay.payment_gateway != 'wsb_credit'
							        AND opay.bank_transfer_mode NOT IN ('cheque_deposited' , 'cheque_failed')
							        AND opay.successfull = 1 
                                GROUP BY opay.order_id ";
				$payment_result = $this->db->query($payment_sql);
				
                // If net payment has been received.
				if(!empty($payment_result->row['total_payment']) && $payment_result->row['total_payment'] > 0){
					
                    $total_payment = (float)$payment_result->row['total_payment'];
                   
                    // Calculate total invoice amount for this order - considering all invoiced and not cancelled suborders
					foreach ($suborder_ids as $suborder_id) {
                        
                        // Ignore if it is a cancelled suborder
						if(!in_array($suborder_id, $cancelled_suborder_ids)){
                            
							$this->db->query("CALL calculateTotalInvoiceAmount('" . $this->db->escape($suborder_id) . "', '" . (int)1 . "', @total_invoice_amount)");
	    					$suborder_invoice_total = (float) $this->db->query("SELECT @total_invoice_amount")->row['@total_invoice_amount'];
                            
                            // adding to total invoice amount for the order
                            $temp_data[$order_id]['invoice_amount'] += $suborder_invoice_total;
						}
					}
                    
					$total_invoice = $temp_data[$order_id]['invoice_amount'];
                    
                    // calculating excess payment
                    $excess_payment                             = (float)($total_payment - $total_invoice);
					$temp_data[$order_id]['excess_payment']     = $excess_payment;

                    // If total invoice amount is more than the total net payment received, then there is no further refund possible.
                    // OR if excess payment is miniscule (less than excess_payment_min_limit), then also no refund needed.
                    // In this case, Marking payment_cleared for this order as NOT_APPLICABLE
					if( $excess_payment < $this->_excess_payment_min_limit ){
                        
                        unset($temp_data[$order_id]);
                        $this->_not_applicable_orders .= ','. $order_id;
                        continue;
                        
					} else { // There is some excess payment that needs to be refunded.
                        
                        //Check if there is already some refund done against this order due to excess payment
                        if ($row['ttl_trxn_amount'] > 0){
                            
                            // parameters against which syncing will be checked
                            $data_to_sync = array();
                            $data_to_sync['order_id']    = $order_id;
                            $data_to_sync['trxn_for']    = 'EXCESS_PAYMENT_BY_CUSTOMER';
                            $data_to_sync['trxn_for_id'] = $order_id;
                            
                            if(!$this->checkTrxnAmtSync($data_to_sync)){
                                // sync match failed. we need to report this error
                                $this->all_error_refunds[$order_id]['order_no']    = $order_no;
                                $this->all_error_refunds[$order_id]['cust_name']   = $row['customer_name'];
                                $this->all_error_refunds[$order_id]['refund_type'] = "Excess Payment";
                                $this->all_error_refunds[$order_id]['ref']         = 'ExcessPayRefund:'.$order_no;
                                $this->all_error_refunds[$order_id]['refund']      = $excess_payment;
                                $this->all_error_refunds[$order_id]['error_ref']   = 'TrxnDetails are not synced with OrderPayment';
                                $this->all_error_refunds[$order_id]['city']        = $row['shipping_city'];
                                
                                // we will investigate and not release payment for this order
                                unset($temp_data[$order_id]);
                                continue;
                            }
                        }
                        
                        // Getting customer details
                        $customer   = array();
                        $cust_details = $this->getCustomerById($row['customer_id']);
                        $customer['cust_id']	         = $row['customer_id'];
                        $customer['cust_name']           = $cust_details['firstname']. ' ' .$cust_details['lastname'];
                        $customer['email'] 	             = $cust_details['email'];
                        $customer['mobile']              = $cust_details['telephone'];
                        $customer['bank_ac_holder_name'] = $cust_details['bank_ac_holder_name'];
                        $customer['bank_ac_number']      = $cust_details['bank_ac_number'];
                        $customer['ifsc_code'] 	         = $cust_details['ifsc_code'];
                        $customer['order_city'] 	     = $row['shipping_city'];
                        
                        $temp_data[$order_id]['customer'] = $customer;
                      
                      
                        // Payment is good to be sent in tenative refunds section for approval
                        $payment_breakup = array();
                        $payment_breakup['ref_id']     = $order_id;
                        $payment_breakup['ref'] 	     = 'ExcessPayRefund:'.$order_no;
                        $payment_breakup['amount']     = $excess_payment;
                        $payment_breakup['date_added'] = $row['order_date'];
                      
                        // filling payment_breakup details in temp data
                        $temp_data[$order_id]['payment_breakup'] = $payment_breakup;
					  
                        // setting temp data for this order id in main data
                        $data[$order_id] = $temp_data[$order_id];
					}
                    
				} else { // No net payment received.
                    
                    unset($temp_data[$order_id]);
                    $this->_not_applicable_orders .= ','. $order_id;
                    continue;
				}
			}
            
            // if there is payments to be refunded
			if(!empty($data)){
				//Transfering tentative refund for approval
				$this->dynamicPaymentRefund($data);
			}
		}
		
	}

	/**
	* Public function to get order list for refundable credit notes
	* @param:  void
	* @return: array
	* @author: Nishu, Oct 2017
	*/
	public function getOrdersForCreditNoteRefund(){

        // SQL
		$sql = "
				SELECT 
				    oo.order_id,
				    oo.order_no,
				    oo.telephone as mobile, 
                    TRIM(CONCAT(oo.firstname, ' ', oo.lastname)) as customer_name, 
				    CONCAT(oo.shipping_city, ', ', oo.shipping_country) AS shipping_city,
				    oo.customer_id,
				    cn.suborder_id,
				    cn.credit_note_amount, 
                    cn.net_refundable, 
				    cn.credit_note_id as cn_id,
				    cn.credit_note_no as cn_no,
				    cn.credit_note_prefix as cn_prefix,
				    cn.date_added as cn_date,
				    cn.payment_cleared,
				    td.trxn_done,
				    IFNULL(SUM(IF(td.trxn_done IN ('NOT_APPLICABLE','BANK_REQUESTED','BANK_PROCESSED','BANK_SUCCESS'), 
			                      IF(td.trxn_amount IS NULL, 0, td.trxn_amount), 
			                      0
			                     )
			                  ), 0) as ttl_trxn_amount
				FROM
				    ".DB_PREFIX."credit_note as cn
				        INNER JOIN
				    ".DB_PREFIX."order as oo ON oo.order_id = cn.order_id 
				        INNER JOIN 
				    ".DB_PREFIX."suborder osub ON osub.order_id = cn.order_id 
				    	LEFT JOIN
				    ".DB_PREFIX."trxn_details as td ON td.trxn_for_id = cn.credit_note_id AND td.trxn_for = 'CREDIT_NOTE'
				WHERE
				    cn.credit_note_status = 1 
				    AND cn.payment_cleared IN ('NO', 'ON_HOLD')
				    AND cn.suborder_id = osub.suborder_id 
				    AND osub.order_status_id > 0 
				    AND osub.order_status_id != 2 
				    AND osub.invoice_no > 0 
				    AND osub.buyer_invoice_id > 0 
				    AND oo.stock_transfer = 0 
				    AND oo.currency_code = 'INR'
				    AND oo.store_id IN (". WSB_STORES_ID .") 
				    AND oo.franchise_id = 0 
				    AND TRIM(LOWER(oo.payment_code)) NOT IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "') 
				GROUP BY cn.credit_note_id
				HAVING  
				    ttl_trxn_amount != net_refundable
			";

		$result = $this->db->query($sql);

        // Initializing result array
		$data = array();
        
		if($result->num_rows > 0 ){

			//CreditNote Class object
			$cn_obj = new CreditNote($this->registry);
            
			foreach ($result->rows as $row) {
				
                // Fetching cn_id (credit_note_id)
				$cn_id      = $row['cn_id'];
                $order_id 	= $row['order_id'];
				$order_no	= $row['order_no'];
                
                // calculating refunds
                $ttl_trxn_amount = !empty($row['ttl_trxn_amount']) ? $row['ttl_trxn_amount'] : 0; // total refund paid already for this CN
                $actual_refundable_amount = !empty($row['net_refundable']) ? $row['net_refundable'] : 0; // total refundable amount for this CN
                $balance_refund_amount = $actual_refundable_amount - $ttl_trxn_amount;

                // Net refundable amount should be greater than min excess payment limit
                // AND Balance refund pending (if any) for this CN must be greater than min excess payment limit
                if( $balance_refund_amount >= $this->_excess_payment_min_limit ) {
			 	
                    //Check if there is already some refund done against this CN
                    //parameters against which checking shall be done
                    $data_to_sync = array();
                    $data_to_sync['order_id']    = $order_id;
                    $data_to_sync['trxn_for']    = 'CREDIT_NOTE';
                    $data_to_sync['trxn_for_id'] = $cn_id;
                    
                    // Sync match failed, we need to report the erro
                    if(!$this->checkTrxnAmtSync($data_to_sync)){
                        $this->all_error_refunds[$cn_id]['order_no']        = $order_no;
                        $this->all_error_refunds[$cn_id]['cust_name']       = $row['customer_name'];
                        $this->all_error_refunds[$cn_id]['refund_type']     = "CREDIT_NOTE";
                        $this->all_error_refunds[$cn_id]['cn_id']           = $cn_id;
                        $this->all_error_refunds[$cn_id]['ref']             = 'CreditNote:'.$row['cn_prefix'].$row['cn_no'];
                        $this->all_error_refunds[$cn_id]['refund']          = $balance_refund_amount;
                        $this->all_error_refunds[$cn_id]['error_ref']       = 'TrxnDetails not synced with order payments';
                        $this->all_error_refunds[$cn_id]['city']            = $row['shipping_city'];
                        continue;
                    }
                    
                    // Getting customer details
                    $cust_details = $this->getCustomerById($row['customer_id']);
                    $customer['cust_id']	= $row['customer_id'];
                    $customer['cust_name']  = $cust_details['firstname']. ' ' .$cust_details['lastname'];
                    $customer['email'] 	    = $cust_details['email'];
                    $customer['mobile']     = $cust_details['telephone'];
                    $customer['bank_ac_holder_name'] = $cust_details['bank_ac_holder_name'];
                    $customer['bank_ac_number'] = $cust_details['bank_ac_number'];
                    $customer['ifsc_code'] 	    = $cust_details['ifsc_code'];
                    $customer['order_city'] 	= $row['shipping_city'];

                    // Payment is good to be sent in tenative refunds section for approval
                    $payment_breakup = array();
                    $payment_breakup['ref_id'] 			= $cn_id;
                    $payment_breakup['ref'] 			= 'CreditNote:'.$row['cn_prefix'].$row['cn_no'];
                    $payment_breakup['amount'] 			= $balance_refund_amount;
                    $payment_breakup['date_added'] 		= $row['cn_date'];

                    // Filling in the final data array
                    $data[$cn_id]['excess_payment']  = $balance_refund_amount;
                    $data[$cn_id]['order_id'] 		 = $order_id;
                    $data[$cn_id]['order_no'] 		 = $order_no;
                    $data[$cn_id]['suborder_id'] 	 = $row['suborder_id'];
                    $data[$cn_id]['cn_no'] 			 = $row['cn_prefix'].$row['cn_no'];
                    $data[$cn_id]['refund_type'] 	 = "CREDIT_NOTE";
                    $data[$cn_id]['customer'] 		 = $customer;
                    $data[$cn_id]['is_approved']     = $row['payment_cleared'];
                    $data[$cn_id]['payment_breakup'] = $payment_breakup;

                    //Handling On_Hold case: Mark CN as pending for approval from tentative refunds
                	$cn_data = array();
                	$cn_data['credit_note_id']  = $row['cn_id'];
                	$cn_data['payment_cleared'] = 'PENDING_APPROVAL';
                	
                	$cn_obj->updateCnPaymentClearedFlag($cn_data);
                    
                } else if (abs($balance_refund_amount) >= $this->_excess_payment_min_limit) { // Case when we have done excess refund and we need to alert internally
                    //Handling On_Hold case: Mark CN as pending for approval from tentative refunds
                	$cn_data = array();
                	$cn_data['credit_note_id']  = $row['cn_id'];
                	$cn_data['payment_cleared'] = 'YES';

                	$cn_obj->updateCnPaymentClearedFlag($cn_data);
                	
                } else {

                	//To update trxn_details and oc-credit_note table for NOT_APPLICABLE
		 			$this->updateTrxnAndCnForNotApplicable($row);

                } 
			}
		}
        
        // if there is payments to be refunded
        if(!empty($data)){
            //Transfering tentative refund for approval
            $this->dynamicPaymentRefund($data);
        }
        
	}



	public function markCnRefundNotApplicable($data){
		//Create Object for BankTransfer class 
		$payment_gateway = new BankTransfer($this->registry); 

		
        //Check If trxn_details are already exist for same refrence
		$trxn_id = $payment_gateway->checkExistingTrxnDetails($data);
		
		if($trxn_id > 0){ //If Exist, then update already existing details
			$mark_not_applicable_sql = "
                                     UPDATE ".DB_PREFIX."trxn_details
                                       SET
                                          trxn_done = 'NOT_APPLICABLE'
                                       WHERE
                                          id = '". (int)$trxn_id ."'
		                           ";
		    //Execute query to mark not_applicable
			$this->db->query($mark_not_applicable_sql);

		}else{ //Else create new enteries for trxn_details
			$data['trxn_done'] = 'NOT_APPLICABLE';
            $trxn_id = $payment_gateway->addTrxnDetails($data);
		}
	}

	/**
	 * Public function to move payments data to Tenative Refund for approval
	 * @param:  array
	 * @return: void
	 * @author: Nishu, Oct 2017
	*/
	public function dynamicPaymentRefund($data){
        
		$payment_breakup = array();
        
		foreach ($data as $value) {
			$order_id        = $value['order_id'];
			$order_no        = $value['order_no'];
            $excess_payment  = $value['excess_payment'];
			$payment_breakup = $value['payment_breakup'];
            
			if($excess_payment >= $this->_excess_payment_min_limit){
                
				$tentativeData                  = array();
				$tentativeData['ref_id']        = $value['payment_breakup']['ref_id'];
				$tentativeData['refund_type']   = $value['refund_type'];
				$tentativeData['order_id']      = $order_id;
				$tentativeData['order_no']      = $order_no;
				$tentativeData['total_refund']  = $excess_payment;
				$tentativeData['user_id']       = 0;
				$tentativeData['user'] 			= 'System Generated';

				if($value['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
					$tentativeData['reference'] = "Excess payment: Refund of Rs ".round($excess_payment, 2)." against overpayment done";
					$tentativeData['trxn_for'] 	= 'EXCESS_PAYMENT_BY_CUSTOMER';
					$tentativeData['suborder_id'] = '';
				}else{
					$tentativeData['reference'] = "GR Credit Note: Refund of Rs ".round($excess_payment, 2)." done against GR, Ref: ".$value['cn_no'];
					$tentativeData['trxn_for'] 	  = 'CREDIT_NOTE';
					$tentativeData['suborder_id'] = $value['suborder_id'];
				}
				$tentativeData['refund_ref']  = $payment_breakup['ref'];

				if(!empty($value['is_approved']) && $value['is_approved'] == 'ON_HOLD'){
					$tentativeData['is_approved']  = 3;					
				}
                
				//Add Tentative refunds, Needs approval to initiate refund
                $tentative_refund_id = $this->addTentativeRefunds($tentativeData);
                
                if ($tentative_refund_id) { // an entry has been created
                    $mail_data = array();
                    $mail_data['order_no']    = $order_no;
                    $mail_data['cust_name']   = $value['customer']['cust_name'];
                    $mail_data['cust_email']  = $value['customer']['email'];
                    $mail_data['refund']      = $excess_payment;
                    $mail_data['refund_type'] = $value['refund_type'];
                    $mail_data['refund_ref']  = $value['payment_breakup']['ref'];
                    $mail_data['city']        = $value['customer']['order_city'];
                    
                    //Collect data in array to send internal mail
                    $this->all_tentative_refunds[] = $mail_data;

                    //Update ExcessPaymentFlag in oc_order if it's an excess payment order
                    if($value['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER") {
                    	$this->_pending_approval_orders .= ','. $order_id;
                    }
                }
			}
		}
	}

    /**
     * Sending mail for all refund payments to customer
     * @param void
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailTentativeRefundApproval($data){
        $html = MailTemplate::sendMailTentativeRefundApproval($data);
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');

        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);

        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->Subject = 'Customers Tentative Refund Payments for Approval - ' . date('d/M/Y H:i:s', time());
        $mail->msgHTML($html);
        $mail->send();
    } //End of sendMailTentativeRefundApproval

    /**
     * Sending mail Error Alert for all tentative refund payments to customer, having any issue
     * @param $data Array
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailErrorRefund($data){
    	$html = MailTemplate::mailErrorRefundPayments($data);
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->addAddress(EMAIL_IDS['nishu']['email_id'], EMAIL_IDS['nishu']['name']);
        $mail->Subject = 'ALERT: Errors In Tentative Refund Payments - ' . date('d/M/Y H:i:s', time());
        $mail->msgHTML($html);
        $mail->send();
    } //End of sendMailErrorRefund

    /**
	 * Public function to check syncing between oc_trxn_details and oc_order_payment table
     * If they are in sync, that implies that we have automatically accounted for the refund 
     * already done via automatic refund system, in recalculating excess payment / cn amount 
	 * @param: $data Array
	 * @return Boolen
	 * @author Nishu, Nov 2017
    */
    public function checkTrxnAmtSync($data){
    	$sql = "SELECT td.id, td.trxn_amount, oop.amount
    	          FROM ".DB_PREFIX."trxn_details td 
    	        	LEFT JOIN 
    	        ".DB_PREFIX."order_payment as oop ON oop.trxn_id = td.id
    	        WHERE 
    	        	td.trxn_for = '".$this->db->escape($data['trxn_for'])."'
    	        	AND td.trxn_for_id = '".(int)$data['trxn_for_id']."'
    	        	AND oop.order_id = '". (int)$data['order_id'] ."'";
    	$result = $this->db->query($sql);
    	if($result->num_rows > 0){
    		foreach ($result->rows as $row) {
    			if(abs($row['trxn_amount']) != abs($row['amount'])){
	    			return false;
	    		}
    		}
    	}
        
        // if we are here that means either there are trxn_details entry, 
        // or all entries match between the two tables.
        return true;
    }

    /**
	 * Public function to add data for tentative refunds
	 * @param: $data Array
	 * @return: void
	 * @author: Nishu, Dec 2017
    */
    public function addTentativeRefunds($data){
        
        // IF there is any entry in tentative Refund for this 
        // reference id, which is not yet cleared, and slated for approval/ approved, 
        // then we will first wait for that payment to be cleared, 
        // and then add this transaction for further approval
    	$check_sql = "
    				  SELECT * from ".DB_PREFIX."tentative_refund 
    				  WHERE 
    				    ref_id = '". (int)$data['ref_id'] ."'
    		            AND order_id = '". (int)$data['order_id'] ."'
    		            AND refund_ref = '". $this->db->escape($data['refund_ref']) ."' 
                        AND is_cleared = 0 
                        AND is_approved != 2
    				";
    	$result = $this->db->query($check_sql);
        
    	if($result->num_rows > 0){
    		return 0;
    	}
        
        // No pending entry for further clearing
    	$sql = "INSERT INTO ".DB_PREFIX."tentative_refund
    		     SET
    		       refund_type = '". $this->db->escape($data['refund_type']) ."',
    		       ref_id = '". (int)$data['ref_id'] ."',
    		       refund_ref = '". $this->db->escape($data['refund_ref']) ."',
    		       order_id = '". (int)$data['order_id'] ."',
    		       order_no = '". $this->db->escape($data['order_no']) ."',
    		       suborder_id = '". $this->db->escape($data['suborder_id']) ."',
    		       total_refund = '". (float)$data['total_refund'] ."',
    		       date_added = NOW(),
    		       reference = '". $this->db->escape($data['reference']) ."',
    		       user_id = '0',
    		       user = '". $this->db->escape($data['user']) ."'
    		       
    	       ";
    	if(!empty($data['is_approved'])){
    		$sql .= ", is_approved = '".(int)$data['is_approved']. "'";
    	}
    	$id = $this->db->query($sql);
    	return $id;
    }

    
    /**
     * Public function to get customer details by customer id
     * @param: $cust_id Integer
     * @return array
     * @author Nishu, 2017
    */
    public function getCustomerById($cust_id = 0){
        $data = array();

        // check for provided customer id
        if(!empty($cust_id)){
            $sql = "SELECT * FROM ". DB_PREFIX . "customer WHERE customer_id = ". (int)$cust_id;
            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                $data = $query->row;
            }
        }
        return $data;
    }

    /**
	 * @info: Public method to get tentative_refund_id (tr_id)
	 *         By given Ref_id and refund_type
	 * @author: Nishu, May 2018
	*/
	public static function getTentativeIdByRefIdAndType($db, $data = array()){
		$result_data = array();
		if(!empty($data)){
			$sql = "
					SELECT 
						id, is_approved
					FROM
						". DB_PREFIX . "tentative_refund
					WHERE
						refund_type     = '". $db->escape($data['refund_type']) ."'
						AND ref_id      = ". $db->escape($data['ref_id']) ."
						AND is_cleared != 1
				  ";
			$result = $db->query($sql);
			
			if($result->num_rows > 0){
				$result_data = $result->row;
			}
		}
		
		return $result_data;
	}

	/**
	 * @info: Public method to mark tentative refund as rejected, till it is not cleared
	 *         By given Ref_id and refund_type
	 * @author: Nishu, May 2018
	*/
	public static function rejectTentativeRefundByRefIdAndType($db, $data = array()){
		//Check if $data is empty
		if(!empty($data)){
			$sql = "
					UPDATE
						". DB_PREFIX . "tentative_refund
					SET
						is_approved = 2
					WHERE
						refund_type     = '". $db->escape($data['refund_type']) ."'
						AND ref_id      = ". (int)$data['ref_id'] ."
						AND is_cleared != 1
				  ";
			$result = $db->query($sql);
		}
	}

}//End of Class
