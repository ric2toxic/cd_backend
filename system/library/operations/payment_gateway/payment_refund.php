<?php
declare(strict_types = 1);

require_once(DIR_SYSTEM.'library/operations/orders/order_payment.php');
/**
 * 	PaymentGateway
 *  @info: this Payment Gateway is abstract class so, those methods which are same for citrus and rezorPay we can wright here so, code redendency is reduce. Same method is use for many class.
 * 	@author @Nishu, Dec 2017
 */
class PaymentRefund
{
	public $registry;
	public $config;
	public $load;
	public $db;
    
	//Paymnet Gateway sequence for refunding amount
	private $_online_payment_gateway  = array('Citrus');
    private $_offline_payment_gateway = 'BankTransfer';
    
	private $_bank_transfer_payments = array();
	private $all_online_pg_refunds   = array();
	private $all_error_refunds       = array();
	 
	private $_cleared_excess_payment_orders = '';
	private $_cleared_credit_note_payments  = '';
    
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
	 * Public function to intiate refund against approved tentative refunds
	 * @param: void
	 * @return: void
	 * @author: Nishu, Oct 2017
	 */
	public function executeRefund() : void {
        
        // Get all approved tentative refunds and dynamically refund them by online PG
		$this->getApprovedRefunds();

		// Refund the remaining payments by Offline PG
		$this->paymentRefundByBankTransfer();

        // Send a summary mail internally for all the successfull Online PG payments done
		if(!empty($this->all_online_pg_refunds)){
			$this->sendMailOfOnlinePGRefunds($this->all_online_pg_refunds);
		}

        // If there are any refunds with errors, which were not processed; they are mailed internally for investigation
		if(!empty($this->all_error_refunds)){
			$this->sendMailErrorRefund($this->all_error_refunds);
		}
		
		// Update cleared excess payments orders with YES
		if($this->_cleared_excess_payment_orders != ''){
		  $order_ids = trim($this->_cleared_excess_payment_orders, ',');
		  OrderEdit::updateExcessPayment($this->db, $order_ids, 'YES');
		}

		//Mark payement_cleared field as YES in oc_credit_note table
		if($this->_cleared_credit_note_payments != ''){

		    $cn_data = array();
	    	$cn_data['payment_cleared'] = 'YES';
	    	$cn_ids = trim($this->_cleared_credit_note_payments, ',');
	    	$cn_data['credit_note_id']  = $cn_ids;
	    	
	    	$cn_obj = new CreditNote($this->registry);
    		$cn_obj->updateCnPaymentClearedFlag($cn_data);
		}
       
		//Check If still there is any tentative refund under process, after completion of refund script
		$this->checkForRefundUnderProcess();

        die("Completed");
	}

	/**
	 * Public function to get tentative approved refunds for access payment
	 * @param: void
	 * @return: array
	 * @author: Nishu, Sept 2017
	 */
	public function getApprovedRefunds() : void{
		$order_id 	= 0;
		$customer 	= array();
		$sql = "SELECT 
					tr.id as tr_id,
					tr.refund_ref,
					tr.refund_type,
					tr.ref_id,
					tr.total_refund,
				    tr.suborder_id,
				    o.order_id,
				    o.order_no,
				    o.customer_id,
				    o.email,
				    o.date_added as order_date,
				    o.payment_code,
				    o.telephone as mobile,
				    CONCAT(o.shipping_city,
				            ', ',
				            o.shipping_country) AS shipping_city,
				    cust.bank_ac_holder_name,
				    cust.bank_ac_number,
				    cust.ifsc_code,
				    cust.firstname,
				    cust.lastname, 
				    cust.ws_access_token as access_token, 
                    GROUP_CONCAT(DISTINCT oss.crm_user_id) as crm_user_id,
                    GROUP_CONCAT(DISTINCT oss.name) as sales_staff_name 
				FROM
				    " . DB_PREFIX . "tentative_refund as tr
				        INNER JOIN
				    " . DB_PREFIX . "order o ON o.order_id = tr.order_id 
				        LEFT JOIN
				    " . DB_PREFIX . "order_sales_staff ooss ON ooss.order_id = o.order_id
				        LEFT JOIN
				    " . DB_PREFIX . "sales_staff oss ON oss.staff_id = ooss.sales_staff_id
				        INNER JOIN
				    " . DB_PREFIX . "customer cust ON cust.customer_id = o.customer_id
				WHERE
				    tr.is_approved = 1 
				        AND tr.is_cleared = 0 
					    AND ( (tr.refund_type = 'EXCESS_PAYMENT_BY_CUSTOMER' AND o.payment_cleared = 'PENDING_APPROVAL') 
                               OR tr.refund_type = 'CREDIT_NOTE'
                            )
					    AND o.stock_transfer = 0 
					    AND o.currency_code = 'INR'
					    AND o.store_id IN (". WSB_STORES_ID .") 
					    AND o.franchise_id = 0 
                        AND TRIM(LOWER(o.payment_code)) NOT IN ('" .implode("', '", CREDIT_PAYMENT_CODES) . "') 
				GROUP BY tr.id
				ORDER BY o.order_id ASC, tr.id ASC 
				";

		$result = $this->db->query($sql);

		if($result->num_rows > 0 ){
			
            $data = array();
            
			foreach ($result->rows as $key => $row) {
                
                $tr_id = (int)$row['tr_id'];
                
                //Check if calculated excess amount is greater than 0
				if ( (float)$row['total_refund'] >= $this->_excess_payment_min_limit ){
                    
                    $temp_data = array();
					$payment_breakup = array();
                    
                    $order_id 					= $row['order_id'];
					$order_no					= $row['order_no'];
                    $total_refund               = $row['total_refund'];
                    // customer details
					$customer['customer_id']	= $row['customer_id'];
					$customer['cust_name']		= $row['firstname']. ' ' .$row['lastname'];
					$customer['access_token']	           = $row['access_token'];
					$customer['email'] 					   = $row['email'];
					$customer['mobile'] 				   = $row['mobile'];
					$customer['bank_ac_holder_name'] 	   = $row['bank_ac_holder_name'];
					$customer['bank_ac_number'] 		   = $row['bank_ac_number'];
					$customer['ifsc_code'] 				   = $row['ifsc_code'];
					$customer['order_city'] 			   = $row['shipping_city'];

					//Update is_cleared = 3 flag in oc_tentative_refund for under process
                	$is_already_under_process = $this->markTentativeRefundUnderProcess((int)$tr_id);
                	if($is_already_under_process){
                		$this->all_error_refunds[$tr_id]['order_no']    = $order_no;
					 	$this->all_error_refunds[$tr_id]['cust_name']   = $customer['cust_name'];
					 	$this->all_error_refunds[$tr_id]['refund_type'] = $row['refund_type'];
					 	$this->all_error_refunds[$tr_id]['ref']         = $row['refund_ref'];
					 	$this->all_error_refunds[$tr_id]['refund']      = $total_refund;
					 	$this->all_error_refunds[$tr_id]['error_ref']   = 'This TentativeRefund is already in process.';
					 	$this->all_error_refunds[$tr_id]['city']        = $row['shipping_city'];
						continue;
                	}

                    // temp data
					$temp_data[$tr_id]['order_id'] 		   = $order_id;
					$temp_data[$tr_id]['order_no'] 		   = $order_no;
					$temp_data[$tr_id]['crm_user_id']      = $row['crm_user_id'];
					$temp_data[$tr_id]['sales_staff_name'] = $row['sales_staff_name'];
                    $temp_data[$tr_id]['ref_id']           = $row['ref_id'];
					$temp_data[$tr_id]['refund_type']      = $row['refund_type'];
					$temp_data[$tr_id]['customer'] 		   = $customer;
					$temp_data[$tr_id]['tr_id']	           = $row['tr_id'];
					$temp_data[$tr_id]['suborder_id']      = $row['suborder_id'];
                    
                    // check if data is synced or not
					$data_to_sync = array();
				 	$data_to_sync['order_id']    = $order_id;
				 	$data_to_sync['trxn_for_id'] = $row['ref_id'];
				 	$data_to_sync['trxn_for']    = $row['refund_type'];

			 		if(!$this->checkTrxnAmtSync($data_to_sync)){
			 			$this->all_error_refunds[$tr_id]['order_no']    = $order_no;
					 	$this->all_error_refunds[$tr_id]['cust_name']   = $customer['cust_name'];
					 	$this->all_error_refunds[$tr_id]['refund_type'] = $row['refund_type'];
					 	$this->all_error_refunds[$tr_id]['ref']         = $row['refund_ref'];
					 	$this->all_error_refunds[$tr_id]['refund']      = $total_refund;
					 	$this->all_error_refunds[$tr_id]['error_ref']   = 'TrxnDetails are not synced with OrderPayment';
					 	$this->all_error_refunds[$tr_id]['city']        = $row['shipping_city'];
						continue;
			 		}else{
			 		    //Get successfully refunded amount from trxn_details by using tr_id
			 		    $refunded_amt = PaymentGatewayBase::getTtlAmtOfSuccessTentativeRefundDone($this->db, $tr_id);
			 		    if($refunded_amt > $total_refund){
			 		    	$this->all_error_refunds[$tr_id]['order_no']    = $order_no;
						 	$this->all_error_refunds[$tr_id]['cust_name']   = $customer['cust_name'];
						 	$this->all_error_refunds[$tr_id]['refund_type'] = $row['refund_type'];
						 	$this->all_error_refunds[$tr_id]['ref']         = $row['refund_ref'];
						 	$this->all_error_refunds[$tr_id]['refund']      = $total_refund;
						 	$this->all_error_refunds[$tr_id]['error_ref']   = 'Successfully Refunded (Rs. '. $refunded_amt .') amount in trxn_details is greater then calculated refund (Rs. '. $total_refund .') amount in tentative refund table';
						 	$this->all_error_refunds[$tr_id]['city']        = $row['shipping_city'];
							continue;
			 		    }else if( $refunded_amt > 0 ){
			 		    	$total_refund = $total_refund - $refunded_amt;
			 		    }
			 		}
                    
                    //Check if calculated refund amount is greater than 0
					if ( (float)$total_refund > $this->_excess_payment_min_limit ){

	                    // payment breakup array
						$payment_breakup['ref_id'] 		      = $row['ref_id'];
						$payment_breakup['ref'] 	          = $row['refund_ref'];
						$payment_breakup['amount'] 		      = $total_refund;
						$payment_breakup['date_added'] 	      = $row['order_date'];
						$temp_data[$tr_id]['ref']             = $row['refund_ref'];
						$temp_data[$tr_id]['excess_payment']  = $total_refund;
						$temp_data[$tr_id]['payment_breakup'] = $payment_breakup;

	                    // populating return data array
						$data[$tr_id] = $temp_data[$tr_id];
					}else{
						// Mark tentative refund as is_cleared = 1
                		$this->updateTentativeRefund((int)$tr_id, 1, 1);

                		// Mark excess payment in order table 'YES', if it is an excess_payment case
		                if ($row['refund_type'] == 'EXCESS_PAYMENT_BY_CUSTOMER') {
		                    $this->_cleared_excess_payment_orders .= ','. $order_id;
		                }else{
		                 //Mark Payment cleared flag for CREDIT_NOTE
		                    $this->_cleared_credit_note_payments .= ','. $row['ref_id'];;
		                }
					}
					
                } else{

					// These are invalid tentative refunds. Marking is_cleared = 2
	                $this->updateTentativeRefund((int)$tr_id, 1, 2);
                    
                    // alert internally about this error
                    $this->sendMailWrongTentativeRefund((int)$tr_id, 2);
				}
			}
            
            // If there are tentative refunds to do
			if(!empty($data)){
				//Dynamically making payments as per various PG priority
				$this->dynamicPaymentRefund($data);
			}
		}
	}

	/**
	 * Public function to release payment using Payment Gateway
	 * @param:  array
	 * @return: void
	 * @author: Nishu, Oct 2017
	*/
	public function dynamicPaymentRefund(array $data) : void{
        // Loop over the Tentative Refund ID(s)
		foreach ($data as $tentative_refund) {
            
            // Get order_id for this tentative refund
            $order_id = $tentative_refund['order_id'];
            $order_no = $tentative_refund['order_no'];
            
            $balance_refund_pending = $tentative_refund['excess_payment'];
            
            // Loop over online payment gateways
            foreach ($this->_online_payment_gateway as $payment_gateway) {
            
                // create payment_gateway object
                $pg_obj = new $payment_gateway($this->registry);
                
                // Get All Successfull Online Payment Gateway Transactions
                $merchant_txn_ids = $pg_obj->getSuccessfullMerchantTxnIdWithBalance($order_id);
                $count_merchant_txn_ids = count($merchant_txn_ids);
                $counter = 0;
                
                // Refund and loop until PG Transactions are over
                while (
                	$balance_refund_pending >= $this->_excess_payment_min_limit 
                	&& !empty($merchant_txn_ids[$counter])
                ) {
                  
                    // initializing history comment
                    $comment = '';
                    
                    // balance with the current merchant txn id
                    $merchant_txn_id         = $merchant_txn_ids[$counter]['merchant_txn_id'];
                    $merchant_txn_id_balance = (float)$merchant_txn_ids[$counter]['balance'];

                    // Check if the online merchant txn id with highest balance isnt able to refund this in one shot
                    if($balance_refund_pending > $merchant_txn_id_balance){
                    	break;
                    }
                    
                    // refund data array to be passed on to Payment gateway
                    $refund_data = array();
                    $refund_data['user_id'] 		= 0;
                    $refund_data['refund_amount'] 	= round(min($balance_refund_pending, $merchant_txn_id_balance), 2);
                    $refund_data['merchant_txn_id'] = $merchant_txn_id;
                    $refund_data['trxn_for'] 	    = $tentative_refund['refund_type'];
                    $refund_data['trxn_for_id']     = $tentative_refund['ref_id'];
                    
                    // Creating comment for history and payment_ref
                    if($tentative_refund['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
                        $comment = "Excess payment: Refund of Rs ".$refund_data['refund_amount']." against overpayment done via " . 
                                    $payment_gateway . " Merchant Txn ID: " . $merchant_txn_id;
                    }else{
                        $comment = "GR Credit Note: Refund of Rs ".$refund_data['refund_amount']." done against GR, Ref: " . 
                                    $tentative_refund['payment_breakup']['ref'].", via " . $payment_gateway . " Merchant Txn ID: " . $merchant_txn_id;
                    }
                    $refund_data['payment_ref'] = $comment;
                    $refund_data['tr_id']       = $tentative_refund['tr_id'];

                    // Refunding by PG
                    $response = $pg_obj->paymentRefund($refund_data);
                    
                    // If response received
                    if(!empty($response)){
                        
                        // If response is SUCCESS
                        if($response['status'] == "success"){
                            
                            // Adjusting balance refund pending
                            $refunded_amount         = abs($refund_data['refund_amount']);
                            $balance_refund_pending -= $refunded_amount;
                            
                            // Response in array format from online PG
                            $response_arr = json_decode($response['response']);
							
                            // Send mail to Customer for Successful PG payment						
                            $mail_data = array();
                            $mail_data['order_no']   = $order_no;
                            $mail_data['cust_name']  = $tentative_refund['customer']['cust_name'];
                            $mail_data['cust_email'] = $tentative_refund['customer']['email'];
                            $mail_data['refund']     = $refunded_amount;
                            $mail_data['payment_gateway'] = $payment_gateway; 
                            $mail_data['merchant_txn_id'] = @$response_arr->merchantTxnId;
                            $mail_data['refund_type'] = $tentative_refund['refund_type'];
                            $mail_data['refund_ref']  = $tentative_refund['payment_breakup']['ref'];
                            $mail_data['city']        = $tentative_refund['customer']['order_city'];
                            
                            // send to Customer about successfull Refund done
                            $this->sendMailOfOnlinePGRefundsToCustomer($mail_data);
                            
                            //Collect data in array to send internal mail
                            $this->all_online_pg_refunds[] = $mail_data; 
													
                            //Update Refund payment in order history
                            $historyData                    = array();
                            $historyData['comment'] 		= $comment;
                            $historyData['notes'] 	 		= serialize($response);
                            $historyData['order_id'] 		= $order_id;
                            $historyData['suborder_id']     = ($tentative_refund['refund_type'] == "CREDIT_NOTE" ? $tentative_refund['suborder_id'] : '');
                            $historyData['notify_email'] 	= 0;
                            $historyData['notify_sms'] 		= 0;
                            $historyData['user'] 			= 'System Generated';
                            //Add Order History for refund details tracking
                            $this->load->model('checkout/order', 'frontend');
                            if (method_exists($this->registry, 'get')) {
                                $checkout_order = $this->registry->get('frontend_model_checkout_order');
                            } else {
                                $checkout_order = $this->registry->frontend_model_checkout_order;
                            }
                            $checkout_order->addOrderHistory($historyData);

                            
                            //Send SMS for Refund Initiated to Customer
                            $this->load->language('account/sms_templates');
                            $mobile_no 	= $tentative_refund['customer']['mobile'];
                            $message 	= '';
                            if($tentative_refund['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
                                $message = sprintf($this->load->language->get('cust_refund_excess_payment'), $refunded_amount, $order_no);
                            }else{
                                $message = sprintf($this->load->language->get('cust_refund_CN'), $refunded_amount, $order_no);
                            }
                            $send_sms = new SMS($message,$mobile_no);
                            $send_sms->sendMessage();
                        }
                    }
                    
                    // Move to next Merchant Txn ID
                    $counter++;
                    
                } // end while loop over merchant Txn ids in a PG
                
            } // end loop over online PGs
            
            // If there is still pending refund amount balance, then we pay by offline PG
            if ( $balance_refund_pending >= $this->_excess_payment_min_limit ) {
                
                $offline_payment = array();
                $pb                         = $tentative_refund['payment_breakup'];
                $pb['amount'] 	            = $balance_refund_pending;
                $offline_payment['payment_breakup']		= $pb;
                $offline_payment['tr_id']               = $tentative_refund['tr_id'];
                $offline_payment['customer']    		= $tentative_refund['customer'];
                $offline_payment['order_id']     		= $order_id;
                $offline_payment['order_no']     		= $order_no;
                $offline_payment['refund']	    		= $balance_refund_pending;
                $offline_payment['refund_type']			= $tentative_refund['refund_type'];
                $offline_payment['ref']                 = $tentative_refund['ref'];
                $offline_payment['sales_staff_name']    = $tentative_refund['sales_staff_name'];
                $offline_payment['crm_user_id']         = $tentative_refund['crm_user_id'];

                $this->_bank_transfer_payments[$tentative_refund['tr_id']] = $offline_payment;
                
            } else { // This tentative refund has been successfully cleared by online PG
                
                // Mark excess payment in order table 'YES', if it is an excess_payment case
                if ($tentative_refund['refund_type'] == 'EXCESS_PAYMENT_BY_CUSTOMER') {
                    $this->_cleared_excess_payment_orders .= ','. $order_id;
                }else{
                    $this->_cleared_credit_note_payments .= ','. $tentative_refund['ref_id'];;
                }
                
                // Mark tentative refund as is_cleared = 1
                $this->updateTentativeRefund((int)$tentative_refund['tr_id'], 1, 1);

            }
            
        } // End loop over Tentative Refund IDs
	
    } // End function
    

    /**
	 * Function to release payment by direct bank trafer (expoting .csv)
	 * @param:  void
	 * @return: void
	 * @author: Nishu, Oct 2017
    */
	public function paymentRefundByBankTransfer() : void {
		if(!empty($this->_bank_transfer_payments)){
	        $obj = new BankTransfer($this->registry);
			$obj->paymentRefund($this->_bank_transfer_payments);			
		}
    }

    /**
     * Sending mail for all refund payments to customer by Online PG, with details to customer
     * @param void
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailOfOnlinePGRefundsToCustomer(array $data) : void {
        
        $html = MailTemplate::mailOnlinePGRefundPaymentsToCustomer($data);
        
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
        $mail->addAddress($data['cust_email'], $data['cust_name']);
        $mail->Subject = 'Successful Refund for your Order ' . $data['order_no'].' - ' . date('d/M/Y H:i:s');
        $mail->msgHTML($html);
        $mail->send();
        
    } //End of sendMailOfOnlinePGRefundsToCustomer

    /**
     * Sending email to internal team, for all the successfull payment refunds 
     * done by Online payment gateway(s)
     * @param void
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailOfOnlinePGRefunds(array $data) : void{
        
        $html = MailTemplate::mailOnlinePGRefundPayments($data);
        
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
        $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->Subject = 'Customer Refund Payments (Online PG) - ' . date('d/M/Y H:i:s', time());
        $mail->msgHTML($html);
        $mail->send();
    } //End of sendMailOfOnlinePGRefunds


    /**
     * Sending mail for All refunds which have error, to Internal team
     * These are generally the cases where syncing has failed between trxn_details and order_payment table
     * @param $data Array
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailErrorRefund(array $data) : void{
        
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
        $mail->Subject = 'ALERT: Errors In Refund Payments - ' . date('d/M/Y H:i:s', time());
        $mail->msgHTML($html);
        $mail->send();
    } //End of sendMailErrorRefund
    
    
    /**
     * Sending mail internally, due to an error, in which a tentative_refund 
     * has been marked is_cleared = <some invalid value> (not 0,1)
     * @param $data Array
     * @return void
     * @author Nishu, Nov 2017   
    */
    public function sendMailWrongTentativeRefund(int $tr_id, int $is_cleared) : void {
        
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
        $mail->Subject = 'ALERT: Wrong Tentative Refund found - ' . date('d/M/Y H:i:s', time());
        $body = "Tenative Refund ID: " . (int)$tr_id . ", marking is_cleared = " . (int)$is_cleared;
        $mail->Body = $body;
        $mail->send();
    } //End of sendMailErrorRefund


    /**
	 * Public function to check syncing between oc_trxn_details and oc_order_payment table
	 * @param: $data Array
	 * @return Boolen
	 * @author Nishu, Nov 2017
    */
    public function checkTrxnAmtSync(array $data) : bool{
    	$sql = "SELECT td.id, td.trxn_amount, oop.amount
    	          FROM " . DB_PREFIX . "trxn_details td 
    	        	LEFT JOIN 
    	        " . DB_PREFIX . "order_payment as oop ON oop.trxn_id = td.id
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
    		
        return true;
    }

    /**
     * Public function to update is_approved and is_cleared flag in oc_tentative_refund
     * @param: $tr_id, $is_approved, $is_cleared
     * @return: void
     * @author: Nishu, Dec 2017
    */
    public function updateTentativeRefund(int $tr_id, int $is_approved = 0, int $is_cleared = 0) : void {
    	$sql = "UPDATE 
    		      " . DB_PREFIX . "tentative_refund
    			SET 
    			   is_approved = ". (int)$is_approved .",
    			   is_cleared  = ". (int)$is_cleared ."
    			 WHERE id = '". (int)$tr_id ."'
    			";
        $this->db->query($sql);
        return;
    }

    /**
     * Private function to update is_cleared = 3 flag in oc_tentative_refund for under process
     * @param: $tr_id
     * @return: Boolean
     * @author: Nishu, Dec 2017
    */
    public function markTentativeRefundUnderProcess(int $tr_id) : bool{
    	$is_already_under_process = true;

    	if(!empty($tr_id)){
    		$lock_query = "LOCK TABLES " . DB_PREFIX . "tentative_refund WRITE;";
        	$this->db->query($lock_query);

	    	$sql = "SELECT
	    				id
	    			FROM 
	    			  " . DB_PREFIX . "tentative_refund
	    		    WHERE 
	    		      id = '". (int)$tr_id ."'
	    			  AND is_cleared = 0
	    			";
	        $result = $this->db->query($sql);
	        if($result->num_rows > 0){
	        	
	        	//is_cleared = 3 means that refund is under_process
	        	$update_sql = " UPDATE
				    			  " . DB_PREFIX . "tentative_refund
				    			SET
				    			  is_cleared = 3
				    		    WHERE 
				    		      id = '". (int)$tr_id ."'
				    			";
	            $this->db->query($update_sql);
	            
	        	$is_already_under_process = false;
	        }

        	//Unlock table 
	        $unlock_query = "UNLOCK TABLES;";
        	$this->db->query($unlock_query);
    	}

        return $is_already_under_process;
    }

    /**
     * Private method to check, is there any refund under process
     * @author: Nishu. Aug 2018
    */
    private function checkForRefundUnderProcess() : void {
    	$sql = "
                SELECT
                   id,
                   ref_id,
                   refund_ref,
                   order_id,
                   order_no,
                   suborder_id,
                   total_refund,
                   updated_by_user
                FROM
                   " . DB_PREFIX . "tentative_refund
                WHERE
                   is_cleared = 3
    	       ";
    	$result = $this->db->query($sql);
    	if($result->num_rows > 0){

    		$html = MailTemplate::mailcheckForRefundUnderProcess($result->rows);
        
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
	        $mail->Subject = 'ALERT: Tentative Refund under process found - ' . date('d/M/Y H:i:s', time());
	        $mail->msgHTML($html);
	        $mail->send();
    	}

    	return;
    }

}//End of Class
