<?php
require_once(__DIR__ . '/../../../config.php');
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/phpmailer.php');
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_clusters.php');

class SellerPayments{

    private $_db   = null;
    private $_mail = null;
    
    private $RETURNED_PAYMENTDATE_GAP_THRESHOLD    = 30; // To alert if invoice date has crossed this many days for this suborder, 
                                                         // with pending returns closure, hence no payment released
    
    // Wait for this many days after order delivery to raise payment
    // Global - 8 days
    private $DELIVERYDATE_GAP = array('global' => 6, 
                                       58392   => -999, // Chitrangana (I39_JP - need to make it invoice+1) 
                                       17174   => -999, // Gopalas International (U29_JP - need to make it invoice+1) 
                                       204141  => -999, // Vintage Cothing Company (as per Rohit)
                                       125158  => 6, // (Ref: Prabhav email) 
                                       60061   => 6, 
                                       39360   => 6, 
                                       5931    => 8, // P96_JP - Club Factory Seller. Increased as per Chandan 
                                       209078  => 2, // 001_KA - As per mail from Vikas (and Rohit)
                                       721     => 0, // check mail from Returns - No Return seller
                                       35831   => 0, // check mail from Returns - No Return seller
                                       46524   => 0, // check mail from Returns - No Return seller
                                       51515   => 0, // check mail from Returns - No Return seller
                                       61705   => 0, // check mail from Returns - No Return seller
                                     );
                                     
                                            
    private $RELEASE_ANYHOW_INVOICES = array();
    private $RELEASE_RECHECK_SUBORDERS = array();
    private $SOR_FREEZED_INVOICES = array(246, 247, 283, 300, 308, 330, 352, 369, 380, 394, 
                                          402, 403, 405, 418, 529, 530, 552, 553, 659, 660, 715); 
    
    private $PYMT_SHEET_FOLDER = '/var/payment-sheet/';
    private $STANC_PYMT_OUT_FOLDER = '/opt/standard-chartered/host2host/documents/payments/out/wholesale-h2h/';
    private $STANC_ACK_REJ_BASE_FOLDER = '/opt/standard-chartered/host2host/documents/payments/in/';
    
    // Order statuses
    private $DELIVERED_STATUS = 15;
    private $FAILED_STATUSES = array(0,2,6,8); // 0 - Missing; 2 - Canceled, 6 - Client Dispute, 8 - Failed
    private $SHIPPED_STATUSES = array(4,13,14,15,17); // 4-Out for Delivery, 13-Shipped, 14-Shipped with tracking, 15-Delivered, 17-Delivery Issues
    private $PARCEL_LOST_STATUSES = array(11); // 11 - Parcel Lost
    
    // Various arrays
    private $_failed_suborders = array(); // Suborders in $FAILED_STATUSES
    private $_parcel_lost_suborders = array(); // Suborders whose parcels are lost in transit
    private $_noinfo_sellers = array();
    private $_long_pending_returns_suborders = array(); // Suborders for which return has pending for too long
    
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
                }
            }
        }
    }
    
    
    
    /**
     * Method to check and initialize a $seller_id key in $payments array
     * It takes care of all the keys to be created for a $seller_id
     * Everytime, a new payment/adjustment avenue is added to this code, this method 
     * needs to be enhanced as well, to initialize the new keys.
     * @author Madhur, 2018
     */
    private function checkAndInitializePaymentsArrayForSellerId(&$payments, $seller_id) {
		
		if ( !isset($payments[$seller_id]) ) {
			
			$payments[$seller_id] = array();
            $payments[$seller_id]['seller_invoice']      = array();
            $payments[$seller_id]['sor_invoice']         = array();
            $payments[$seller_id]['wsb_purchase']        = array();
            $payments[$seller_id]['seller_debit_note']   = array();
            $payments[$seller_id]['wsb_purchase_return'] = array();
                
        }
    }
    
    
    /**
     * Method to mark all seller invoice(s) and debit note(s) as FAILED_ORDER, 
     * which have been fully returned back to the seller.
     * This method works only on those case(s), where there only one seller invoice 
     * and one debit note corresponding to a suborder_id and seller_id.
     * Moreover, payments of both the seller invoice and debit note has not been cleared yet.
     * Also, invoice value is exactly equal to the debit note value.
     * All such invoice(s) and debit note(s) are marked FAILED_ORDER.
     * @author Madhur, 2018
     */
    private function markAllFullyReturnedInvoiceAndDebitNotes() {
        
        // First, we find all fully returned seller invoice and its
        // corresponding debit note. Works only for the case with 1 SI and 1 DN
        $sql = "SELECT osdn.order_id, 
                       osdn.suborder_id, 
                       osdn.seller_id, 
                       osdn.debit_note_id, 
                       osdn.trxn_done AS debit_note_trxn_done, 
                       osi.seller_invoice_id,
                       osi.trxn_done AS seller_invoice_trxn_done, 

                       ( SELECT SUM(oopsi.quantity*oopsi.piece_in_set*oopsi.transfer_price_per_piece) AS seller_invoice_amount 
                         FROM " . DB_PREFIX. "order_product oopsi 
                         WHERE oopsi.seller_invoice_id = osi.seller_invoice_id 
                           AND oopsi.seller_id = osdn.seller_id 
                           AND oopsi.suborder_id = osdn.suborder_id 
                           AND oopsi.order_id = osdn.order_id
                       ) AS seller_invoice_amount, 
       
                       ( SELECT SUM(ort.quantity*oopdn.transfer_price_per_piece) AS debit_note_amount 
                         FROM " . DB_PREFIX. "return ort 
                         INNER JOIN " . DB_PREFIX. "order_product oopdn ON oopdn.order_product_id = ort.order_product_id 
                         WHERE ort.debit_note_id = osdn.debit_note_id 
                           AND oopdn.seller_id = osdn.seller_id 
                           AND oopdn.suborder_id = osdn.suborder_id 
                           AND oopdn.order_id = osdn.order_id
                       ) AS debit_note_amount 
           
                FROM  " . DB_PREFIX . "seller_debit_note osdn 
                INNER JOIN " . DB_PREFIX . "seller_invoice osi ON osi.order_id = osdn.order_id  
                
                WHERE osi.suborder_id = osdn.suborder_id 
                  AND osi.seller_id = osdn.seller_id 
                  AND osdn.debit_note_status = 1 
                  AND (osdn.custom_id = 0 OR osdn.custom_id IS NULL) 
                  AND osi.trxn_done != 'INVALID_NO_GOODS'
                
                GROUP BY osdn.suborder_id, osdn.seller_id
                
                HAVING COUNT(DISTINCT osdn.debit_note_id) = 1 
                   AND COUNT(DISTINCT osi.seller_invoice_id) = 1
                   AND seller_invoice_amount = debit_note_amount 
                   AND debit_note_trxn_done = 'NOT_DONE' 
                   AND seller_invoice_trxn_done = 'NOT_DONE'";
        $query = $this->_db->query($sql);
        
        // Marking the invoice(s) and debit_note(s) as FAILED_ORDER
        foreach ($query->rows as $row) {
            
            // seller invoice - FAILED_ORDER
            $inv_failed_sql = "UPDATE " . DB_PREFIX . "seller_invoice 
                               SET trxn_done = 'FAILED_ORDER' 
                               WHERE seller_invoice_id = '" . (int)$row['seller_invoice_id'] . "' 
                                 AND order_id = '" . (int)$row['order_id'] . "' 
                                 AND suborder_id = '" . $this->_db->escape($row['suborder_id']) . "' 
                                 AND seller_id = '" . (int)$row['seller_id'] . "' 
                                 AND trxn_done = 'NOT_DONE'";
            $this->_db->query($inv_failed_sql);
            
            // seller debit note - FAILED_ORDER
            $sdn_failed_sql = "UPDATE " . DB_PREFIX . "seller_debit_note  
                               SET trxn_done = 'FAILED_ORDER' 
                               WHERE debit_note_id = '" . (int)$row['debit_note_id'] . "' 
                                 AND order_id = '" . (int)$row['order_id'] . "' 
                                 AND suborder_id = '" . $this->_db->escape($row['suborder_id']) . "' 
                                 AND seller_id = '" . (int)$row['seller_id'] . "' 
                                 AND debit_note_no > 0 
                                 AND debit_note_status = 1 
                                 AND trxn_done = 'NOT_DONE'";
            $this->_db->query($sdn_failed_sql);
            
        }
    }
    
   /**
    * Method for getting all those seller invoices, for which 
    * payment is not yet done
    * @author: Vikas, 2017
    */
    private function getSellerInvoicesToPay(){

        $sql = "SELECT si.seller_invoice_id, 
                       si.order_id,
                       si.suborder_id,
                       si.seller_id,
                       si.seller_invoice_no,
                       si.seller_invoice_prefix,
                       si.date_added, 
                       DATEDIFF(NOW(),si.date_added) AS invoice_payment_date_gap, 
                       SUM(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS amount, 
                       GROUP_CONCAT(DISTINCT oop.order_product_id) AS order_product_ids, 
                       o.order_no, 
                       osub.order_status_id, 
                       COALESCE(DATEDIFF(NOW(), osub.delivered_date), -1) AS daysafterdelivered, 
                       COALESCE(MAX(oopst.limit_days), 0) AS sor_limit_days 
                FROM " . DB_PREFIX . "seller_invoice si 
                INNER JOIN " . DB_PREFIX . "order_product oop ON oop.seller_invoice_id = si.seller_invoice_id 
                 LEFT JOIN " . DB_PREFIX . "order_product_sor_terms oopst ON oopst.order_product_id = oop.order_product_id 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = oop.order_id 
                INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id 
                INNER JOIN " . DB_PREFIX . "ms_seller oms 
                        ON oms.seller_id = si.seller_id AND 
                           oms.block_payment = 0 
                WHERE si.suborder_id = oop.suborder_id 
                  AND si.order_id = oop.order_id 
                  AND si.seller_id = oop.seller_id 
                  AND osub.suborder_id = oop.suborder_id 
                  AND si.trxn_done = 'NOT_DONE' 
                  AND oms.seller_invoice_generate = 1 
                  AND ( osub.order_status_id NOT IN (" . implode(',',$this->FAILED_STATUSES) . ") ";
        
        if ( !empty($this->RELEASE_RECHECK_SUBORDERS) ) {
            $sql .= " OR osub.suborder_id IN ('" . implode("','",$this->RELEASE_RECHECK_SUBORDERS) ."') ";
        }
        
        $sql .= " )  
                  AND o.stock_transfer = 0 
                GROUP BY si.seller_invoice_id 
                ORDER BY si.order_id ASC";
        $invoices = $this->_db->query($sql)->rows;
        return $invoices;
	}
    
    
   /**
    * Method for getting all those SOR products, for which
    * payment is not yet done
    * @author: Vikas, 2017
    */
    private function getSorInvoicesToPay(){

        // At first, we populate wsb_sor_payment table, for the order product id(s), which are invoiced 
        // and not recorded in the sor_payment table yet
        $sql = "SELECT wsbp.seller_id, 
                       oop.wsb_purchase_id, 
                       oop.order_id, 
                       oop.suborder_id, 
                       oop.seller_invoice_id, 
                       GROUP_CONCAT(DISTINCT oop.order_product_id) as order_product_ids 
                FROM " . DB_PREFIX . "order_product oop 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = oop.order_id 
                INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id 
                INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp ON wsbp.purchase_id = oop.wsb_purchase_id 
                INNER JOIN " . DB_PREFIX . "ms_seller oms ON oms.seller_id = oop.seller_id 
                WHERE oop.sor_product = 1 
                  AND oop.wsb_purchase_id > 0 
                  AND oop.seller_invoice_id > 0
                  AND (oop.sor_payment_id = 0 OR oop.sor_payment_id IS NULL)  
                  AND oop.edit_type IN ('SELLER_APPROVED', 'SELLER_PARTIAL', 'DAMAGE_BY_COURIER_COMPANY', 'TRANSFER_TO_WSB_BOOKS') 
                  AND osub.invoice_no > 0 
                  AND osub.suborder_id = oop.suborder_id 
                  AND oms.seller_invoice_generate = 0 
                  AND wsbp.sor_purchase = 1 
                  AND wsbp.purchase_id NOT IN (" . implode(',', $this->SOR_FREEZED_INVOICES) . ") 
                  AND o.stock_transfer = 0 
                GROUP BY oop.suborder_id, oop.seller_invoice_id, oop.wsb_purchase_id";
        
        $sor_payment_ids_to_create = $this->_db->query($sql)->rows;
        
        // Now populating them in the sor_payment table and also updating the order product table
        foreach ($sor_payment_ids_to_create as $id) {
            $sql = "INSERT INTO " . DB_PREFIX . "wsb_sor_payment 
                    SET order_id             = '" . (int)$id['order_id'] . "', 
                        suborder_id          = '" . $this->_db->escape($id['suborder_id']) . "',  
                        seller_id            = '" . (int)$id['seller_id'] . "', 
                        wsb_purchase_id      = '" . (int)$id['wsb_purchase_id'] . "',  
                        type                 = 'invoice',  
                        type_id              = '" . (int)$id['seller_invoice_id'] . "',  
                        trxn_done            = 'NOT_DONE'";
            $this->_db->query($sql);
            $sor_payment_id = $this->_db->getLastId();
            
            //updating in order product
            if (!empty($sor_payment_id)) {
                $sql = "UPDATE " . DB_PREFIX . "order_product 
                        SET sor_payment_id = '" . (int)$sor_payment_id . "' 
                        WHERE order_product_id IN (" . $id['order_product_ids'] . ") 
                          AND seller_invoice_id = '" . (int)$id['seller_invoice_id'] . "' 
                          AND order_id = '" . (int)$id['order_id'] . "' 
                          AND suborder_id = '" . $this->_db->escape($id['suborder_id']) . "' 
                          AND wsb_purchase_id = '" . (int)$id['wsb_purchase_id'] . "'";
                $this->_db->query($sql);
            }
        }
        
        $sor_invoices = array();
        // Finally getting the SOR invoices whose payment is pending, and which are not failed yet
        $sql = "SELECT wsbsp.sor_payment_id, 
                       wsbsp.order_id,
                       wsbsp.suborder_id,
                       wsbsp.wsb_purchase_id, 
                       wsbsp.seller_id,
                       wsbsp.type, 
                       wsbsp.type_id as seller_invoice_id, 
                       SUM(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS amount, 
                       GROUP_CONCAT(DISTINCT oop.order_product_id) AS order_product_ids, 
                       o.order_no, 
                       osub.order_status_id, 
                       COALESCE(DATEDIFF(NOW(), osub.delivered_date), -1) AS daysafterdelivered, 
                       COALESCE(MAX(oopst.limit_days), 0) AS sor_limit_days, 
                       wsbp.invoice_no as seller_invoice_no, 
                       '' as seller_invoice_prefix, 
                       DATE(wsbp.invoice_date) as invoice_date, 
                       0 as invoice_payment_date_gap 
                FROM " . DB_PREFIX . "wsb_sor_payment wsbsp 
                INNER JOIN " . DB_PREFIX . "order_product oop ON oop.sor_payment_id = wsbsp.sor_payment_id 
                 LEFT JOIN " . DB_PREFIX . "order_product_sor_terms oopst ON oopst.order_product_id = oop.order_product_id 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = oop.order_id 
                INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id 
                INNER JOIN " . DB_PREFIX . "wsb_purchase wsbp ON wsbp.purchase_id = wsbsp.wsb_purchase_id 
                INNER JOIN " . DB_PREFIX . "ms_seller oms 
                        ON oms.seller_id = wsbsp.seller_id AND 
                           oms.block_payment = 0 
                WHERE wsbsp.order_id = oop.order_id 
                  AND wsbsp.suborder_id = oop.suborder_id 
                  AND wsbsp.wsb_purchase_id = oop.wsb_purchase_id 
                  AND wsbsp.type_id = oop.seller_invoice_id 
                  AND wsbsp.type = 'invoice' 
                  AND wsbsp.trxn_done = 'NOT_DONE' 
                  AND oop.sor_product = 1 
                  AND oop.suborder_id = osub.suborder_id 
                  AND osub.order_status_id NOT IN (" . implode(',',$this->FAILED_STATUSES) . ") 
                  AND osub.invoice_no > 0 
                  AND o.stock_transfer = 0 
                  AND wsbp.sor_purchase = 1 
                  AND wsbp.purchase_id NOT IN (" . implode(',', $this->SOR_FREEZED_INVOICES) . ") 
                  AND wsbsp.seller_id IN (209078)
                  AND wsbp.purchase_id IN (881) 
                GROUP BY wsbsp.sor_payment_id 
                ORDER BY o.order_id ASC";
        $sor_invoices = $this->_db->query($sql)->rows;
        return $sor_invoices;

        /* 721,21343,21963,28373,28981,35831,51515,84690,84711,91102,151314 */
    }
    
    
    /**
     * Method to remove all those invoices from the $invoices_topay 
     * for which return request has been received, and it is not CLOSED marked yet.
     * @author Madhur, 2017-18.
     */
    private function removeReturnsWithoutDebitNoteSuborders(&$invoices_topay) {
        
        if ( !empty($invoices_topay) ) {
            
            // Loop over invoices
            foreach ($invoices_topay as $key => $invoice) {
                
                // Ignore if payment for this invoice has to be released anyhow.
                if ( in_array($invoice['seller_invoice_id'], $this->RELEASE_ANYHOW_INVOICES) ) {
                    continue;
                }
                
                // Ignore if payment for this suborder has to be released/adjusted anyhow.
                if ( in_array($invoice['suborder_id'], $this->RELEASE_RECHECK_SUBORDERS) ) {
                    continue;
                }
                
                // Check if there is any return against these order_product_ids, 
                // and it is still not marked closed.
				$sql = "SELECT return_id 
						FROM " . DB_PREFIX . "return 
						WHERE order_product_id IN (" . $invoice['order_product_ids'] . ") 
						  AND active_row = 1 
						  AND return_action_id NOT IN (" . implode(',', CLOSED_ACTION_IDS) . ") 
						LIMIT 1";
				$query = $this->_db->query($sql);
			
				if ($query->num_rows) { // There are some Un-CLOSED returns
					
					// check if this invoice's payment has been DUE for too long.
					if ( $invoice['invoice_payment_date_gap'] >= $this->RETURNED_PAYMENTDATE_GAP_THRESHOLD 
                         && $invoice['sor_limit_days'] <= 0 ) {
						$this->_long_pending_returns_suborders[] = $invoice['suborder_id'];
					}
					
					unset($invoices_topay[$key]);
					continue;
				}
            }
            
            // Rebase the $invoices_topay as unset operations distort the keys - causes issues in later stages
            $invoices_topay = array_values($invoices_topay);            
            
            // Alert about long pending returns closure
            $this->_long_pending_returns_suborders = array();
            if ( !empty($this->_long_pending_returns_suborders) ) {
                
                $this->_long_pending_returns_suborders = array_unique($this->_long_pending_returns_suborders);
                
                // Mail Internally
                $this->prepareMailerObject();
                
                $this->_mail->addAddress('returns@wholesalebox.in', 'Returns WholesaleBox');
                $this->_mail->addAddress('prabhav.jain@wholesalebox.in', 'Prabhav Jain');
                $this->_mail->addCC('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
                $this->_mail->addCC('chandan.agarwal@wholesalebox.in', 'Chandan Agarwal');
                $this->_mail->addCC('vikas.jain@wholesalebox.in', 'Vikas Jain');

                $this->_mail->Subject = 'Attn:: Long Pending Returns - ' . date('d/M/Y', time());
                
                $body = "Howdy !! \n\n";
                $body .= "Following Suborders have Returns not closed for quite a long time. "; 
                $body .= "Hence, the payment has not been released to seller, despite invoice generated. " . $this->RETURNED_PAYMENTDATE_GAP_THRESHOLD . " day \n \n";
                $i = 1;
                foreach ($this->_long_pending_returns_suborders as $sid) {
                    
                    $body .= $i. ' Suborder: ' . $sid . " \n\n ";
                    $i++;
                }
                $body .= "\n";
                $body .= "Ciao !!\n";
                $body .= "Seller Payments Bot";
                $this->_mail->Body = $body;
                $this->_mail->send(1,false);
            }
                
        }
    }
    
    
    /**
     * Method to remove all those suborders from the $invoices_topay 
     * which has not been delivered yet, OR if delivered, has not 
     * yet crossed the $DELIVERYDATE_GAP
     * @author Madhur, 2017
     */
    private function removeNonDeliveredSuborders(&$invoices_topay) {

        // Now we loop over suborder_ids to check the days passed since delivery
        foreach ($invoices_topay as $key => $invoice) {
            
            // Ignore if payment for this invoice has to be released anyhow.
            if ( in_array($invoice['seller_invoice_id'], $this->RELEASE_ANYHOW_INVOICES) ) {
                continue;
            }
            
            // Ignore if payment for this suborder has to be released/adjusted anyhow.
            if ( in_array($invoice['suborder_id'], $this->RELEASE_RECHECK_SUBORDERS) ) {
                continue;
            }
            
            // If order_status_id = PARCEL_LOST_STATUSES then we clear the invoice payment 
            // and alert about parcel lost status internally
            if ( in_array((int)$invoice['order_status_id'], $this->PARCEL_LOST_STATUSES) ) {
                $this->_parcel_lost_suborders[$invoice['suborder_id']] = $invoice['order_no'];
                continue;
            }
            
            // Check if deliverydate_gap has not passed the threshold + sor_limit_days
            $dd_thres = (int)($this->DELIVERYDATE_GAP[$invoice['seller_id']] ?? $this->DELIVERYDATE_GAP['global'] ?? 6);
            if ( (int)$invoice['daysafterdelivered'] < ($dd_thres + (int)$invoice['sor_limit_days']) ) {
                // Remove this invoice from current payments to be done as delivery threshold not passed yet
                unset($invoices_topay[$key]);
                continue;
            }
        }
        
        // Rebase the $invoices_topay as unset operations distort the keys - causes issues in later stages
        $invoices_topay = array_values($invoices_topay);
        
        // Alert internally for the Parcel lost in transit Suborders whose payment is getting released.
        if ( !empty($this->_parcel_lost_suborders) ) {
            
            // Mail Internally
            $this->prepareMailerObject();
            $this->_mail->addAddress('operations@wholesalebox.in', 'Operations WholesaleBox');
            $this->_mail->addAddress('chandan.agarwal@wholesalebox.in', 'Chandan Agarwal');
            $this->_mail->addCC('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
            $this->_mail->addCC('accounts@wholesalebox.in', 'Accounts WholesaleBox');
            $this->_mail->addCC('vikas.jain@wholesalebox.in', 'Vikas Jain');
            $this->_mail->Subject = 'Parcels LOST in Transit - Seller Payment(s) Released - ' . date('d/M/Y', time());
            $body = "Howdy !! \n\n";
            $body .= "Following suborder(s) have been marked Parcel LOST in Transit. \n";
            $body .= "Please check them and block the payment(s) if not true, and get their statuses corrected. \n"; 
            $body .= "Payments to seller(s) for these suborder(s) is getting released. Please check: \n";
            $i = 1;
            foreach ($this->_parcel_lost_suborders as $suborder_id => $order_no) {
                
                $body .= $i.'. Order: ' . $order_no . 
                            ', Suborder: ' . $suborder_id . "\n";
                $i++;
            }
            $body .= "\n";
            $body .= "Ciao !!\n";
            $body .= "Seller Payments Bot";
            $this->_mail->Body = $body;
            $this->_mail->send(1,false);
        }
    }
    
    private function breakBySellerId(&$payments, $invoices, $key){
        
        if(!empty($invoices)){
            
            foreach ($invoices as $invoice){
				
				// initialize if this seller_id is encountered for first time in $payments array
				$this->checkAndInitializePaymentsArrayForSellerId($payments, $invoice['seller_id']);
                
                // populating invoice data
                $payments[$invoice['seller_id']][$key][] = $invoice;
            }
        }
    }
    
    
    /**
    * Method for getting all those debit_notes, for which payment 
    * has not been adjusted yet
    * @author: Vikas, 2017
    */
    private function getSellerDebitNotes(){

        $sql = "SELECT sdn.debit_note_id, 
                       sdn.order_id,
                       sdn.suborder_id,
                       sdn.seller_id, 
                       sdn.debit_note_prefix, 
                       sdn.debit_note_no, 
                       sdn.date_added, 
                       o.order_no 
                FROM " . DB_PREFIX . "seller_debit_note  sdn 
                INNER JOIN " . DB_PREFIX . "order o ON o.order_id = sdn.order_id 
                INNER JOIN " . DB_PREFIX . "ms_seller oms 
                        ON oms.seller_id = sdn.seller_id AND 
                           oms.block_payment = 0 
                WHERE sdn.debit_note_status = 1 
                  AND sdn.debit_note_no > 0 
                  AND (sdn.custom_id = 0 OR sdn.custom_id IS NULL) 
                  AND sdn.trxn_done = 'NOT_DONE' 
                ORDER BY sdn.seller_id, sdn.debit_note_id ASC";
        $debit_notes = $this->_db->query($sql)->rows;
        return $debit_notes;
    }
    
    
   /**
    * Method for getting all those wsb_purchase_return debit_notes, for which payment 
    * has not been adjusted yet
    * @author: Madhur, 2018
    */
    private function getWsbPurchaseReturnDebitNotes(){
		
		// Initializing
		$debit_notes = array();
		$debit_notes_with_mismatch = array();
		$debit_note_ids = array();
		$debit_note_ids_with_mismatch = array();
		$debit_note_ids_with_payment_cleared = array();
        
        $sql = "
				SELECT 
				    owpr.debit_note_id, 
                    owpr.purchase_id, 
                    owpr.debit_note_prefix, 
                    owpr.debit_note_no, 
                    owpr.date_added, 
                    IF(owpr.debit_note_status = 1, owpr.debit_note_amount, 0) AS debit_note_amount, 
                    owp.seller_id, 
                    owp.invoice_no, 
				    COALESCE(SUM(otd.trxn_amount), 0) as ttl_trxn_amount 
			                     
				FROM " . DB_PREFIX . "wsb_purchase_return owpr 
				INNER JOIN " . DB_PREFIX . "wsb_purchase owp ON owp.purchase_id = owpr.purchase_id 
				INNER JOIN " . DB_PREFIX . "ms_seller oms 
				        ON oms.seller_id = owp.seller_id AND 
				           oms.block_payment = 0 
				LEFT JOIN " . DB_PREFIX . "trxn_details otd ON otd.trxn_for_id = owpr.debit_note_id 
				                                               AND otd.trxn_for = 'WSB_PURCHASE_RETURN' 
				                                               AND otd.trxn_done IN ('NOT_APPLICABLE',
				                                                                     'BANK_REQUESTED',
				                                                                     'BANK_PROCESSED',
				                                                                     'BANK_SUCCESS')
				WHERE owpr.debit_note_no > 0 
                  AND owpr.payment_cleared = 'NO' 
                  AND owp.sor_purchase = 0 
                  				
                GROUP BY owpr.debit_note_id 
				
				ORDER BY owp.seller_id ASC, owpr.debit_note_id ASC 
			";
		$query = $this->_db->query($sql);
        
        if ($query->num_rows) {
			
			$debit_notes = $query->rows;
			$debit_note_ids = array_column($debit_notes, 'debit_note_id');
			
			// Check if debit_note_amount value stored in DB is correct
			// It should be as per the wsb_purchase_return_breakup stored in DB
			$sql_check = "SELECT owpr.debit_note_id, 
								 owpr.debit_note_amount, 
                                 SUM(owprb.transfer_price_per_piece*owprb.quantity) as breakup_debit_note_amount 
                      
                          FROM " . DB_PREFIX . "wsb_purchase_return owpr 
                          INNER JOIN " . DB_PREFIX . "wsb_purchase_return_breakup owprb ON owpr.debit_note_id = owprb.debit_note_id 
                          WHERE owpr.debit_note_id IN (" . implode(',',$debit_note_ids) . ") 
                          GROUP BY owpr.debit_note_id 
                          HAVING ABS(owpr.debit_note_amount - breakup_debit_note_amount) >= 0.01";
            $query_check = $this->_db->query($sql_check);
            
            if ($query_check->num_rows) { // there are debit_note_ids having amount mismatch
				$debit_notes_with_mismatch = $query_check->rows;
				$debit_note_ids_with_mismatch = array_column($debit_notes_with_mismatch, 'debit_note_id');
			}
				
			// We need to unset these debit_notes from the list of debit_notes to be adjusted
			foreach ($debit_notes as $key => $dn) {
				// unsetting the debit_note
				if (in_array($dn['debit_note_id'], $debit_note_ids_with_mismatch)) {
					unset($debit_notes[$key]);
					continue;
				}
				
				// if no mismatch issue with the debit_note, we calculate the actual adjustment/payment amount
				$amount = -abs($debit_notes[$key]['debit_note_amount']) - (float)$debit_notes[$key]['ttl_trxn_amount'];
				
				// If adjustment/payment amount < 0.01, then we consider this DN as payment_cleared
				if ( abs($amount) < 0.01 ) {
					$debit_note_ids_with_payment_cleared[] = $debit_notes[$key]['debit_note_id'];
					unset($debit_notes[$key]);
					continue;
				}
				
				$debit_notes[$key]['amount'] = $amount;
			}
			
			
			// Set payment_cleared to YES for debit_notes with no adjustment/payment amount
			if ( !empty($debit_note_ids_with_payment_cleared) ) {
				$sql_update_payment_cleared = "UPDATE " . DB_PREFIX . "wsb_purchase_return 
				                               SET payment_cleared = 'YES' 
				                               WHERE debit_note_id IN (" . implode(',', $debit_note_ids_with_payment_cleared) . ")";
				$this->_db->query($sql_update_payment_cleared);
			}
			
			// Alert internally about the debit_note_ids with mismatch issue
			if (!empty($debit_notes_with_mismatch)) {
				
				$this->prepareMailerObject();
                $this->_mail->addAddress('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
                $this->_mail->Subject = 'WSB Purchase Return Debit Notes with Mismatch - ' . date("F j, Y, H:i:s");
                $body = "Howdy !! \n\n";
                $body .= "Following WSB Purchase Return Debit Note(s) have amount mismatch with breakup amount. \n";
                $body .= "Please check them at earliest. \n"; 
                $i = 1;
                foreach ($debit_notes_with_mismatch as $dnm) {
                    
                    $body .= $i.'. WSB Purchase Return DN ID: ' . $dnm['debit_note_id'] . 
                                ', DN Amount: ' . $dnm['debit_note_amount'] . 
                                ', Breakup Amount: ' . $dnm['breakup_debit_note_amount'] . "\n";
                    $i++;
                }
                $body .= "\n";
                $body .= "Ciao !!\n";
                $body .= "Seller Payments Bot";
                $this->_mail->Body = $body;
                $this->_mail->send(1,false);
			}
		}
		
		// Rebase the $debit_notes as unset operations distort the keys - causes issues in later stages
		$debit_notes = array_values($debit_notes);
        return $debit_notes;
    }
    
        
    private function updateDebitNoteAmount(&$debit_notes){
        
        foreach ($debit_notes as $key => $debit_note) {
            $sql  = "SELECT sum( oop.transfer_price_per_piece * ocr.quantity ) as amount ";
            $sql .= "FROM ".DB_PREFIX."return ocr ";
            $sql .= "INNER JOIN ".DB_PREFIX."order_product oop ON ( ocr.order_product_id = oop.order_product_id ) ";
            $sql .= "WHERE debit_note_id = '". (int)$debit_note['debit_note_id'] ."'";
            $result = $this->_db->query($sql);
            
            $debit_notes[$key]['amount'] = (float)$result->row['amount'];
        }
    }
    
    private function addSellerDebitNotes(&$payments, $debit_notes) {
        
        foreach ($debit_notes as $debit_note) {
			
			// initialize if this seller_id is encountered for first time in $payments array
			$this->checkAndInitializePaymentsArrayForSellerId($payments, $debit_note['seller_id']);
                
            // WE need to ensure that either the Seller invoice against 
            // this particular debit note is either already paid for 
            // or being paid in this particular payment processing
            $exists = false;
            
            // First, checking if it already exists in the existing seller_invoices in the payments array
            foreach ( $payments[$debit_note['seller_id']]['seller_invoice'] as $key => $invoice ) {
                
                if ( $invoice['order_id']    == $debit_note['order_id'] 
                  && $invoice['suborder_id'] == $debit_note['suborder_id'] 
                  && $invoice['seller_id']   == $debit_note['seller_id'] ) {
                
                    $exists = true;
                    break;
                }
            }
            
            // Check for the seller_invoice trxn_done status in DB if previous check has not returned TRUE
            if ( !$exists ) {
                $sql = "SELECT trxn_done FROM " . DB_PREFIX . "seller_invoice 
                        WHERE order_id = '" . (int)$debit_note['order_id'] . "' 
                          AND suborder_id = '" . $this->_db->escape($debit_note['suborder_id']) . "' 
                          AND seller_id = '" . (int)$debit_note['seller_id'] . "' 
                          AND trxn_done = 'NOT_DONE'";
                $query = $this->_db->query($sql);
                
                if ( $query->num_rows == 0 ) {
                    $exists = true;
                }
            }
            
            if ( $exists ) {
                $payments[$debit_note['seller_id']]['seller_debit_note'][] = $debit_note;
            }
        }
    }
    
    
    /**
     * Generic method to add all various transactions in the 
     * payments array.
     * Method assumes that @param $trxns is an array of individual transactions.
     * Each individual transaction is an array, consisting of 'seller_id' key
     * @param $trxn_type : string representing the type of transaction for payment 
     * is being done/adjusted. eg: 'seller_invoice', 'wsb_purchase', 'wsb_purchase_return', etc.
     * @author Madhur, 2018
     */
    private function addTransactions(&$payments, $trxns, $trxn_type) {
		
		// $trxn_type string cant be empty
		if (empty($trxn_type)) {
			return false;
		}
        
        foreach ($trxns as $trxn) {
			
			// if no seller_id key in $trxn, we move to next
			if (empty($trxn['seller_id'])) {
				continue;
			}
			
			// initialize if this seller_id is encountered for first time in $payments array
			$this->checkAndInitializePaymentsArrayForSellerId($payments, $trxn['seller_id']);
            
            // Populating $payments array
            $payments[$trxn['seller_id']][$trxn_type][] = $trxn;
        }
    }
        
    
   /**
    * Method for getting all those wsb_purchase invoices, for which payment 
    * has not been done/adjusted yet
    * @author: Madhur, 2018
    */
    private function getWsbPurchaseInvoices(){
		
		// Initializing
		$invoices = array();
		$invoices_with_mismatch = array();
		$invoice_ids = array();
		$invoice_ids_with_mismatch = array();
		$invoice_ids_with_payment_cleared = array();
        
        $sql = "
				SELECT 
				    owp.purchase_id, 
                    owp.invoice_no, 
                    owp.invoice_date, 
                    owp.total_purchase_value, 
                    owp.seller_id, 
				    IFNULL(SUM(IF(otd.trxn_done IN ('NOT_APPLICABLE','BANK_REQUESTED','BANK_PROCESSED','BANK_SUCCESS'), 
			                      IF(otd.trxn_amount IS NULL, 0, otd.trxn_amount), 
			                      0
			                     )), 0) as ttl_trxn_amount 
			                     
				FROM " . DB_PREFIX . "wsb_purchase owp 
				INNER JOIN " . DB_PREFIX . "ms_seller oms 
				        ON oms.seller_id = owp.seller_id AND 
				           oms.block_payment = 0 
				LEFT JOIN " . DB_PREFIX . "trxn_details otd ON otd.trxn_for_id = owp.purchase_id 
				                                               AND otd.trxn_for = 'WSB_PURCHASE' 
				WHERE owp.payment_cleared = 'NO' 
                  AND owp.sor_purchase = 0 
                  AND DATEDIFF(NOW(), owp.invoice_date) >= owp.payment_release_invoice_date_gap 
				
                GROUP BY owp.purchase_id  
				
				ORDER BY owp.seller_id ASC, owp.purchase_id ASC 
			";
		$query = $this->_db->query($sql);
        
        if ($query->num_rows) {
			
			$invoices = $query->rows;
			$invoice_ids = array_column($invoices, 'purchase_id');
			
			// Check if total_purchase_value stored in DB is correct
			// It should be as per the wsb_purchase_breakup stored in DB
			$sql_check = "SELECT owp.purchase_id, 
								 owp.total_purchase_value, 
                                 SUM(owpb.transfer_price_per_piece*owpb.pieces) as breakup_invoice_amount 
                      
                          FROM " . DB_PREFIX . "wsb_purchase owp 
                          INNER JOIN " . DB_PREFIX . "wsb_purchase_breakup owpb ON owp.purchase_id = owpb.purchase_id  
                          WHERE owp.purchase_id IN (" . implode(',',$invoice_ids) . ") 
                          GROUP BY owp.purchase_id  
                          HAVING ABS(owp.total_purchase_value - breakup_invoice_amount) >= 0.01";
            $query_check = $this->_db->query($sql_check);
            
            if ($query_check->num_rows) { // there are invoice_ids having amount mismatch
				$invoices_with_mismatch = $query_check->rows;
				$invoice_ids_with_mismatch = array_column($invoices_with_mismatch, 'purchase_id');
			}
				
			// We need to unset these invoices from the list of invoices to be adjusted
			foreach ($invoices as $key => $inv) {
				// unsetting the invoice
				if (in_array($inv['purchase_id'], $invoice_ids_with_mismatch)) {
					unset($invoices[$key]);
					continue;
				}
				
				// if no mismatch issue with the invoices, we calculate the actual adjustment/payment amount
				$amount = (float)$invoices[$key]['total_purchase_value'] - (float)$invoices[$key]['ttl_trxn_amount'];
				
				// If adjustment/payment amount < 0.01, then we consider this invoice as payment_cleared
				if ( abs($amount) < 0.01 ) {
					$invoice_ids_with_payment_cleared[] = $invoices[$key]['purchase_id'];
					unset($invoices[$key]);
					continue;
				}
				
				$invoices[$key]['amount'] = $amount;
			}
			
			
			// Set payment_cleared to YES for invoices with no adjustment/payment amount
			if ( !empty($invoice_ids_with_payment_cleared) ) {
				$sql_update_payment_cleared = "UPDATE " . DB_PREFIX . "wsb_purchase 
				                               SET payment_cleared = 'YES' 
				                               WHERE purchase_id IN (" . implode(',', $invoice_ids_with_payment_cleared) . ")";
				$this->_db->query($sql_update_payment_cleared);
			}
			
			// Alert internally about the invoices with mismatch issue
			if (!empty($invoices_with_mismatch)) {
				
				$this->prepareMailerObject();
                $this->_mail->addAddress('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
                $this->_mail->Subject = 'WSB Purchase Invoices with Mismatch - ' . date("F j, Y, H:i:s");
                $body = "Howdy !! \n\n";
                $body .= "Following WSB Purchase Invoice(s) have amount mismatch with breakup amount. \n";
                $body .= "Please check them at earliest. \n"; 
                $i = 1;
                foreach ($invoices_with_mismatch as $invm) {
                    
                    $body .= $i.'. WSB Purchase Inv ID: ' . $invm['purchase_id'] . 
                                ', Inv Amount: ' . $invm['total_purchase_value'] . 
                                ', Breakup Amount: ' . $invm['breakup_invoice_amount'] . "\n";
                    $i++;
                }
                $body .= "\n";
                $body .= "Ciao !!\n";
                $body .= "Seller Payments Bot";
                $this->_mail->Body = $body;
                $this->_mail->send(1,false);
			}
		}
		
		// Rebase the $invoices as unset operations distort the keys - causes issues in later stages
		$invoices = array_values($invoices);
        return $invoices;
    }
    
    
    public function __construct(){
        
        // Creating DB link object
        $this->_db = new Database\DB(DB_SERVERS);
        $this->_db->useWriteDbOnly();
        
        // Creating PHPMailer object for usage across various stages in this code
        $this->prepareMailerObject();
    }
    
    

    public function main(){

        // First we mark all fully retuned invoice and debit notes as FAILED_ORDER
        // This reduces the invoices and debit notes to consider for payment
        
        $this->markAllFullyReturnedInvoiceAndDebitNotes();
        
        
        $seller_invoices_topay = $this->getSellerInvoicesToPay();
        
        $sor_invoices_topay = $this->getSorInvoicesToPay();   
        
        $this->removeReturnsWithoutDebitNoteSuborders($seller_invoices_topay);
        
        $this->removeReturnsWithoutDebitNoteSuborders($sor_invoices_topay);                

        $this->removeNonDeliveredSuborders($seller_invoices_topay);
        
        $this->removeNonDeliveredSuborders($sor_invoices_topay);
        

        // Initializing $payments array
        $payments = array();
        $this->breakBySellerId($payments, $seller_invoices_topay, 'seller_invoice');
        
        $this->breakBySellerId($payments, $sor_invoices_topay, 'sor_invoice');
        
        
        // Debit notes from oc_seller_debit_note - regular online order debit notes
        $seller_debit_notes = $this->getSellerDebitNotes();
        
        $this->updateDebitNoteAmount($seller_debit_notes);
        
        $this->addSellerDebitNotes($payments, $seller_debit_notes);
        
        
        // WSB Purchase Invoices
        $wsb_purchase_invoices = $this->getWsbPurchaseInvoices();
        
        $this->addTransactions($payments, $wsb_purchase_invoices, 'wsb_purchase');
        
        
        // WSB Purchase Return Debit Notes
        $wsb_purchase_return_debit_notes = $this->getWsbPurchaseReturnDebitNotes();
        
        $this->addTransactions($payments, $wsb_purchase_return_debit_notes, 'wsb_purchase_return');
        
        
        // Miscellaneous Adjustments (if any)
        $this->addMiscellaneousAdjustments($payments);
        
        
        // update total amount to pay / find negative balance (if any)
        $this->updateTotalAmountPerSeller($payments);
        
        
        $seller_infos = $this->getSellersInfo(array_keys($payments));
        
        $this->addSellerInfoToPayments($payments, $seller_infos);
        
        $this->saveInCsv($payments);
    }

    
    
    private function updateTotalAmountPerSeller(&$payments) {
        
        $sellers_with_negative_payment = array();
        
        foreach ($payments as $seller_id => $payment_breakup) {
            
            $total_amount = 0;
            
            if ( !empty($payment_breakup['seller_invoice']) ) {
                foreach ($payment_breakup['seller_invoice'] as $invoice) {
                    
                    $total_amount += (float)$invoice['amount'];
                }
            }
            
            if ( !empty($payment_breakup['sor_invoice']) ) {
                foreach ($payment_breakup['sor_invoice'] as $invoice) {
                    
                    $total_amount += (float)$invoice['amount'];
                }
            }
            
            
            if ( !empty($payment_breakup['wsb_purchase']) ) {
                foreach ($payment_breakup['wsb_purchase'] as $invoice) {
                    
                    $total_amount += (float)$invoice['amount'];
                }
            }
            
            
            if ( !empty($payment_breakup['seller_debit_note']) ) {
                foreach ($payment_breakup['seller_debit_note'] as $debit_note) {
                    
                    $total_amount -= (float)$debit_note['amount'];
                }
            }
            
            if ( !empty($payment_breakup['wsb_purchase_return']) ) {
                foreach ($payment_breakup['wsb_purchase_return'] as $debit_note) {
					
                    // it can be positive or negative - we will need to make this generalized for all
                    $total_amount += (float)$debit_note['amount']; 
                }
            }
            
            if ( !empty($payment_breakup['miscellaneous']) ) {
                foreach ($payment_breakup['miscellaneous'] as $adjustment) {
					
                    $total_amount += (float)$adjustment['amount']; 
                }
            }
            

            if ( $total_amount <= 1 ) { // Payment should be higher than Rs. 1 to be released
                // this will also handle numerical precision error cases, where net sum is 0 but code calculates as 1e-13 etc
                if ($total_amount < 0) { // Need to inform internally for negative balance cases
                    $sellers_with_negative_payment[$seller_id] = $payments[$seller_id];
                    $sellers_with_negative_payment[$seller_id]['total_amount'] = $total_amount;
                }
                
                // Remove from $payments to be done
                unset($payments[$seller_id]);
            } else {
                $payments[$seller_id]['total_amount'] = $total_amount;
            }
        }
        

        // Mail internally if there are sellers with negative payments
        if ( !empty($sellers_with_negative_payment) ) {
            // Mail Internally
            $this->prepareMailerObject();
            $this->_mail->addAddress('chandan.agarwal@wholesalebox.in', 'Chandan Agarwal');
            $this->_mail->addAddress('accounts@wholesalebox.in', 'Accounts WholesaleBox');
            $this->_mail->addAddress('vikas.jain@wholesalebox.in', 'Vikas Jain');
            $this->_mail->addCC('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
            $this->_mail->Subject = 'Sellers with Negative Balance Payments - ' . date('d/M/Y', time());
            $body = "Howdy !! \n\n";
            $body .= "Following Seller Invoice(s) have negative balance payments. Check the severity level. \n"; 
            $body .= "If you are going to manually adjust them, then please inform Madhur, to account for in DB. \n\n";
            
            $seller_infos = $this->getSellersInfo(array_keys($sellers_with_negative_payment));
            $i = 1;
            foreach ($sellers_with_negative_payment as $seller_id => $payment_details) {
                
                $body .= $i.'. Seller: ' . $seller_infos[$seller_id]['nickname'] . 
                                '( ' . $seller_infos[$seller_id]['company'] . 
                                ') - Balance Payment: Rs. ' . $payment_details['total_amount'] . " . Breakup is as follows: \n";
                // Seller invoice - online orders
                foreach ($payment_details['seller_invoice'] as $invoice) {
                    $body .= '        Suborder: ' . $invoice['suborder_id'] . ' -> Inv. Amt: Rs. ' . $invoice['amount'] . "\n";
                }
                // SOR invoice 
                foreach ($payment_details['sor_invoice'] as $invoice) {
                    $body .= '        Suborder: ' . $invoice['suborder_id'] . ' -> SOR Inv. Amt: Rs. ' . $invoice['amount'] . "\n";
                }
                // WSB Purchased Inventory Invoice 
                foreach ($payment_details['wsb_purchase'] as $invoice) {
                    $body .= '        WSB Purchase Inv: ' . $invoice['invoice_no'] . ' -> Inv. Amt: Rs. ' . $invoice['amount'] . "\n";
                }
                // Seller debit note - online orders
                foreach ($payment_details['seller_debit_note'] as $debit_note) {
                    $body .= '        Suborder: ' . $debit_note['suborder_id'] . ' -> DN Amt: Rs. ' . -$debit_note['amount'] . "\n";
                }
                // WSB Purchased Inventory Return Debit Note 
                foreach ($payment_details['wsb_purchase_return'] as $debit_note) {
                    $body .= '        WSB Purchase Return DN: ' . $debit_note['debit_note_prefix'].$debit_note['debit_note_no'] . ' -> DN Amt: Rs. ' . $debit_note['amount'] . "\n";
                }
                
                $body .= "\n";
                $i++;
            }
            $body .= "\n";
            $body .= "Ciao !!\n";
            $body .= "Seller Payments Bot";
            $this->_mail->Body = $body;
            $this->_mail->send(1,false);
        }
    }
    
    /**
     * Method to get seller information such as nickname, 
     * bank account name, bank account number, IFSC code, 
     * email address, given an array of seller_id(s)
     */
    private function getSellersInfo($seller_ids) {
        
        $sellers_info = array();
        
        if (!empty($seller_ids)) {
            // get the data from oc_ms_seller table
            $sql = "SELECT seller_id, 
                           nickname, 
                           company, 
                           bank_ac_holder_name, 
                           bank_ac_number, 
                           ifsc_code 
                    FROM " . DB_PREFIX . "ms_seller 
                    WHERE seller_id IN (" . implode(",",$seller_ids) . ") 
                    ORDER BY seller_id ASC";
            $seller_info_query = $this->_db->query($sql);
            
            if (!$seller_info_query->num_rows)
                return false;
                
            $seller_infos = array_combine(array_column($seller_info_query->rows, 'seller_id'), 
                                          $seller_info_query->rows
                                         );
                                         
            // get email ids for the seller
            $sql = "SELECT customer_id, email 
                    FROM " . DB_PREFIX . "customer  
                    WHERE customer_id IN (" . implode(",",$seller_ids) . ") 
                    ORDER BY customer_id ASC";
            $seller_email_query = $this->_db->query($sql);
            
            foreach ($seller_email_query->rows as $seller_row) {
                $seller_infos[$seller_row['customer_id']]['email'] = $seller_row['email'];
            }
            
            // Hardcoding for Manjaree - as requested by them to have payment advice emailed 
            // to accounts@manjaree.in - Customer/Seller ID = 9230
            if ( isset($seller_infos[9230]) ) {
                $seller_infos[9230]['email'] = 'accounts@manjaree.in';
            }
        }
        
        return $seller_infos;            
    }
    
    private function addSellerInfoToPayments(&$payments, $seller_infos) {
        foreach ($seller_infos as $seller_id => $info) {
            if (isset($payments[$seller_id])) {
                foreach ($info as $key => $value) {
                    $payments[$seller_id][$key] = $value;
                }
            }
        }
    }
    
    
    private function updateTrxnForPaymentRequsted($trxn_for, $trxn_for_id, $trxn_amount, $trxn_bank) {
		
		$trxn_id_updated = 0;
		
		// first find if there are any trxn_id with NOT_DONE status
		$sql = "SELECT * FROM " . DB_PREFIX . "trxn_details 
		        WHERE trxn_done   = 'NOT_DONE' 
		          AND trxn_for    = '" . $this->_db->escape($trxn_for) . "' 
		          AND trxn_for_id = " . (int)$trxn_for_id . " 
		        ORDER BY id ASC ";
		$query = $this->_db->query($sql);
		
		// if there are rows found
		if ($query->num_rows) {
			
			$counter = 1;
			$trxn_details = $query->rows;
			// take the first row and update it; second row onwards - delete them 
			foreach ($trxn_details as $trxn_row) {
				
				if ($counter == 1) { 
					$sql_update = "UPDATE " . DB_PREFIX . "trxn_details 
					               SET trxn_done       = 'BANK_REQUESTED', 
					                   trxn_for        = '" . $this->_db->escape($trxn_for) . "', 
					                   trxn_for_id     = " . (int)$trxn_for_id . ", 
					                   trxn_amount     = " . (float)$trxn_amount . ", 
					                   trxn_bank       = '" . $this->_db->escape($trxn_bank) . "', 
					                   trxn_date_added = NOW() 
					               WHERE id = " . (int)$trxn_row['id'];
					$this->_db->query($sql_update);
					$trxn_id_updated = (int)$trxn_row['id'];
					
				} else { // delete extra rows
					$sql_delete = "DELETE FROM " . DB_PREFIX . "trxn_details 
					               WHERE id = " . (int)$trxn_row['id'];
					$this->_db->query($sql_delete);
					
				}
				
				// Increment counter
				$counter++;    
			}
			
		} else { // No rows found - we need to INSERT
			
			$sql_insert = "INSERT INTO " . DB_PREFIX . "trxn_details 
			               SET trxn_done       = 'BANK_REQUESTED', 
					           trxn_for        = '" . $this->_db->escape($trxn_for) . "', 
					           trxn_for_id     = " . (int)$trxn_for_id . ", 
					           trxn_amount     = " . (float)$trxn_amount . ", 
					           trxn_bank       = '" . $this->_db->escape($trxn_bank) . "', 
					           trxn_date_added = NOW()";
	        $this->_db->query($sql_insert);
			$trxn_id_updated = (int)$this->_db->getLastId();
		}
		
		return $trxn_id_updated; 
	}
	
	
	private function addMiscellaneousAdjustments(&$payments) {
		
		///// ******* MISCELLANEOUS MANUAL ADJUSTMENTS  *****  ///////
        /* array of seller_id => array(array('amount' => <amount>, 
                                             'remarks' => <remarks>, 
                                             'date'    => <date>
                                            )
                                      )
                                       */
                                    
        // ****              DONT TOUCH                       ***** //
        $misc_adj = array();
        // Miscellaneous adjustments done on 14 August 2017 //
        //$misc_adj = array(759 => array(array('amount'  => -1800,  // P21_JP (Uttam Enterprises)
                                             //'remarks' => 'Dup.Pymt-20170613866',
                                             //'date'    => '27/06/2017'
                                            //)
                                      //),
                          //8694 => array(array('amount'  => -300,  // R24_JP (The Choice Fashion Private Limited )
                                              //'remarks' => 'Dup.Pymt-20170614290',
                                              //'date'    => '04/07/2017'
                                             //)
                                       //),
                          //10676 => array(array('amount'  => -1000,  // 005_DL (Swagg Technologies PVT LTD)
                                               //'remarks' => 'Dup.Pymt-20170614290',
                                               //'date'    => '04/07/2017'
                                              //)
                                        //),
                          //24202 => array(array('amount'  => -1305,  // 026_DL (Sri Krisna Unlimited)
                                               //'remarks' => 'Dup.Pymt-20170614290',
                                               //'date'    => '04/07/2017'
                                              //)
                                        //),
                          //27085 => array(array('amount'  => -1620.68,  // 050_DL (Global eTree Services Pvt. Ltd.)
                                               //'remarks' => 'Dup.Pymt-20170614884',
                                               //'date'    => '11/07/2017'
                                               //)
                                        //)
                         //);
                         
        // Miscellaneous adjustments done on 03 October 2017 //
        //$misc_adj = array(16804 => array(array('amount'  => -14297,  // 041_ST (KVSFAB)
                                               //'remarks' => 'VAT mismatch Q1-2017-18',
                                               //'date'    => '03/10/2017'
                                            //)
                                      //)
                         //);
                         
        // Miscellaneous adjustments done on 31 October 2017 //
        //$misc_adj = array(17473 => array(array('amount'  => -3763,  // J11_JP (Highlight Fashion Export)
                                               //'remarks' => 'VAT mismatch Q1-2017-18',
                                               //'date'    => '31/10/2017'
                                              //)
                                        //)
                         //);
                         
        // Miscellaneous adjustments done on 24 November 2017 //
        //$misc_adj = array(28664 => array(array('amount'  => 7640,  // 136_ST (Kyara Fashion)
                                               //'remarks' => 'CNCL DN WSBGJ-DN-2017-18_184 (20170818095)',
                                               //'date'    => '14/09/2017'
                                              //)
                                        //)
                         //);
                         
        // Miscellaneous adjustments done on 17 May 2018 //
        //$misc_adj = array(5218 => array(array('amount'  => -2578.46,  // P82_JP (Aprique Feb)
                                              //'remarks' => 'Prev FY Acct Balance',
                                              //'date'    => '17/05/2018'
                                             //)
                                       //)
                         //);

                         
        // Miscellaneous adjustments done on 01 June 2018 //
        //$misc_adj = array(70117 => array(array('amount'  => 985,  // 226_ST (Zee Textile Vareli Pvt. Ltd.)
                                               //'remarks' => 'CNCL DN GJDN18-113 (20180108331)',
                                               //'date'    => '06/04/2018'
                                              //)
                                        //)
                         //); 
                         
                         
        // Miscellaneous adjustments done on 28 September 2018 //
        //$misc_adj = array(112461 => array(array('amount'  => 12504.8,  // 063_MU (Savvy Fashion)
                                               //'remarks' => 'CNCL DN MHDN18-45 (20180829136)',
                                               //'date'    => '08/09/2018'
                                              //)
                                        //)           
                         //);
                         
        // Miscellaneous adjustments done on 26 October 2018 //
        //$misc_adj = array(74090 => array(array('amount'  => 4867.8,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-764 (20180314449-ST-2)',
                                               //'date'    => '24/05/2018'
                                              //),
                                         //array('amount'  => 6578.25,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-765 (20180415229-ST)',
                                               //'date'    => '24/05/2018'
                                              //),
                                         //array('amount'  => 693.26,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-839 (20180415202-ST)',
                                               //'date'    => '29/05/2018'
                                              //),
                                         //array('amount'  => 787.5,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-842 (20180417631-ST)',
                                               //'date'    => '29/05/2018'
                                              //),
                                         //array('amount'  => 667.89,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-844 (20180418301-ST)',
                                               //'date'    => '29/05/2018'
                                              //),
                                         //array('amount'  => 5446.32,  // 234_ST (Vastradeal India Expo)
                                               //'remarks' => 'CNCL DN GJDN18-863 (20180416291-ST)',
                                               //'date'    => '30/05/2018'
                                              //)
                                        //)           
                         //); 
                         
        // Miscellaneous adjustments done on 30 October 2018 //
        //$misc_adj = array(9230 => array(array('amount'  => 4593.75,  // 002_ST (Manjaree)
                                              //'remarks' => 'CNCL DN GJDN18-2969 (20180832544-ST)',
                                              //'date'    => '23/10/2018'
                                             //)
                                       //)           
                         //);
                         
        // Miscellaneous adjustments done on 30 November 2018 //
        //$misc_adj = array(76213 => array(array('amount'  => 2970, 
                                               //'remarks' => 'CNCL DN GJDN18-2720 (20180831323-ST)',
                                               //'date'    => '08/10/2018'
                                             //)
                                        //)           
                           //);
                         
        // Miscellaneous adjustments done on 04 December 2018 - Wrongly done //
        //$misc_adj = array(76213 => array(array('amount'  => 2970, 
                                               //'remarks' => 'CNCL DN GJDN18-2720 (20180831323-ST)',
                                               //'date'    => '08/10/2018'
                                             //)
                                        //)           
                         //);
                         
        // Miscellaneous adjustments done on 11 December 2018 - Wrongly done //
        //$misc_adj = array(76213 => array(array('amount'  => 2970, 
                                               //'remarks' => 'CNCL DN GJDN18-2720 (20180831323-ST)',
                                               //'date'    => '08/10/2018'
                                             //)
                                        //)           
                         //);
                         
                         
        // Miscellaneous adjustments done on 31 January 2019 //
        //$misc_adj = array(100316 => array(array('amount'  => 648.9,  // 320_ST (Vahanvati Enterprise)
                                                //'remarks' => 'CNCL DN GJDN18-2709 (20180830801)',
                                                //'date'    => '06/10/2018'
                                               //), 
                                          //array('amount'  => 170,  // 320_ST (Vahanvati Enterprise)
                                                //'remarks' => 'CNCL DN GJDN18-2707 (20180726078)',
                                                //'date'    => '06/10/2018'
                                               //) 
                                         //)
                         //); 
                         
         // Miscellaneous adjustments done on 4 February 2019 // 
         //$misc_adj = array(154061 => array(array('amount'  => -155878.01,  // 417_ST (Vansh Creation)
                                                 //'remarks' => 'Excess Pymt - 20190145322',
                                                 //'date'    => '08/01/2019'
                                               //), 
                                          //array('amount'  => -745.5,  // 417_ST (Vansh Creation)
                                                //'remarks' => 'Excess Pymt - 20190145315',
                                                //'date'    => '03/01/2019'
                                               //) 
                                         //)
                         //);
                         
         // Miscellaneous adjustments done on 19 March 2019 //
         //$misc_adj = array(56387 => array(array('amount'  => 732.40,  // 191_ST - Vrinda Silk Mills
                                                //'remarks' => 'CNCL DN GJDN18-3941 (20181141321-ST-2)',
                                                //'date'    => '10/01/2019'
                                              //)
                                        //)           
                          //);
                          
        // Miscellaneous adjustments done on 28 May 2019 //
        //$misc_adj = array(16804 => array(array('amount'  => 6371.64,  // 041_ST (KVSFAB)
                                               //'remarks' => 'CNCL DN GJDN18-3309 (20180933758)',
                                               //'date'    => '15/11/2018'
                                              //)
                                        //), 
                                        
                          //100316 => array(array('amount'  => 4678.1,  // 320_ST (Vahanvati Enterprise)
                                               //'remarks' => 'CNCL DN GJDN18-2209 (20180727040)',
                                               //'date'    => '06/09/2018'
                                               //)
                                         //),
                        
                          //85464 => array(array('amount'  => 7875,  // A45_ST (KAVYA STYLE PLUS)
                                               //'remarks' => 'CNCL DN GJDN18-1330 (20180518899)',
                                               //'date'    => '02/07/2018'
                                              //)
                                        //)
                         //);
                         
        // ****              DONT TOUCH                       ***** //
        
        foreach ($misc_adj as $seller_id => $adj_details) {
            
            if (!isset($payments[$seller_id])) {
				// initialize if this seller_id is encountered for first time in $payments array
				$this->checkAndInitializePaymentsArrayForSellerId($payments, $seller_id);
            }
        
            // Adding Miscellaneous manual payments / adjustments
            $payments[$seller_id]['miscellaneous'] = array();
            
            foreach ($adj_details as $adjustment) {
                $payments[$seller_id]['miscellaneous'][] = array('csv_row' => array('I', 
                                                                                    'MISC', 
                                                                                    $adjustment['remarks'],
                                                                                    (float)$adjustment['amount'],
                                                                                    $adjustment['date']
                                                                                   ), 
                                                                 'amount'  => (float)$adjustment['amount'] 
                                                                );
            }
        }
	}
    
    
    private function saveInCsv($payments){
        
        if (empty($payments)) {
            return false; // No seller payment sheet to be generated
        }
        $filename = 'seller_payments_' . date('dmy') . '.csv';
        $fp = fopen($this->PYMT_SHEET_FOLDER . $filename, 'w');
        
        // Insert into DB
        $sql = "INSERT INTO " . DB_PREFIX . "stanc_payment_csv 
                (`csv_file_path`, `upload_success`, `date_added`) 
                VALUES 
                ('" . $this->_db->escape($this->PYMT_SHEET_FOLDER . $filename) . "', 0, NOW() )";
        $query = $this->_db->query($sql);
        
        $payment_csv_id = $this->_db->getLastId();
        
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
        foreach ( $payments as $sid => $details) {
            
            if ( empty(trim(strtoupper($details['bank_ac_holder_name']))) 
              || empty(trim($details['bank_ac_number'])) 
              || empty(trim(strtoupper($details['ifsc_code']))) 
              || empty((float)$details['total_amount']) 
               ) {
                // Ignore this seller entry
                $this->_noinfo_sellers[$sid] = $details;
                continue;
            }
            
            $payment_ref_no = $details['nickname'].date('dmy');

            $row = array(
                'P',
                'ACH',
                $payment_ref_no,
                '75105102107',
                date('d/m/Y'),
                trim(strtoupper($details['bank_ac_holder_name'])),
                trim($details['bank_ac_number']),
                trim(strtoupper($details['ifsc_code'])),
                (float)$details['total_amount'],
                trim($details['email']),
                'WholesaleBox Order Payment',
                '',
            );
            fputcsv( $fp , $row );
            
            // Array to store key info in db
            $breakup_details = array();
            
            // Seller Invoice
            $breakup_details['seller_invoice'] = array();
            
            if(!empty($details['seller_invoice'])){
                foreach( $details['seller_invoice'] as $invoice ){
                    $row = array(
                        'I', 
                        $invoice['order_no'], 
                        'Invoice: ' . $invoice['seller_invoice_prefix'].$invoice['seller_invoice_no'],
                        (float)$invoice['amount'],
                        date('d/m/Y', strtotime($invoice['date_added'])),
                    );
                    fputcsv( $fp , $row );
                    
                    $sql = "UPDATE " . DB_PREFIX . "seller_invoice 
                            SET trxn_done = 'BANK_REQUESTED', 
                                trxn_amount = '" . (float)$invoice['amount'] . "', 
                                trxn_bank = 'stanc', 
                                trxn_date_added = NOW() 
                            WHERE seller_invoice_id = '" . (int)$invoice['seller_invoice_id'] . "'";
                    $this->_db->query($sql);
                    
                    // For storing in db
                    $breakup_details['seller_invoice'][] = array('suborder_id' => $invoice['suborder_id'], 
                                                                 'seller_invoice_id' => (int)$invoice['seller_invoice_id'], 
                                                                 'amount' => (float)$invoice['amount']
                                                                );
                }
            }
            
            
            // SOR Invoice
            $breakup_details['sor_invoice'] = array();
            
            if(!empty($details['sor_invoice'])){
                foreach( $details['sor_invoice'] as $invoice ){
                    $row = array(
                        'I', 
                        $invoice['order_no'], 
                        'SOR Invoice: ' . $invoice['seller_invoice_prefix'].$invoice['seller_invoice_no'],
                        (float)$invoice['amount'],
                        date('d/m/Y', strtotime($invoice['invoice_date'])),
                    );
                    fputcsv( $fp , $row );
                    
                    $sql = "UPDATE " . DB_PREFIX . "wsb_sor_payment  
                            SET trxn_done = 'BANK_REQUESTED', 
                                trxn_amount = '" . (float)$invoice['amount'] . "', 
                                trxn_bank = 'stanc', 
                                trxn_date_added = NOW() 
                            WHERE sor_payment_id = '" . (int)$invoice['sor_payment_id'] . "'";
                    $this->_db->query($sql);
                    
                    // For storing in db
                    $breakup_details['sor_invoice'][] = array('suborder_id' => $invoice['suborder_id'], 
                                                              'sor_payment_id' => (int)$invoice['sor_payment_id'], 
                                                              'amount' => (float)$invoice['amount']
                                                              );
                }
            }
            
            
            // WSB purchase invoices - purchased inventory
            $breakup_details['wsb_purchase'] = array();

            if(!empty($details['wsb_purchase'])){
                foreach( $details['wsb_purchase'] as $invoice ){
                    
                    // Updating in oc_trxn_details
                    $trxn_id = $this->updateTrxnForPaymentRequsted('WSB_PURCHASE', 
                                                                   (int)$invoice['purchase_id'], 
                                                                   (float)$invoice['amount'], 
                                                                   'stanc');
                    if (!$trxn_id) { // something gone wrong. continue to next invoice. Need to investigate manually
						continue;
					}
                    
                    // CSV 'I' row
                    $row = array(
                                 'I',
                                 'WSB-PUR',
                                 'Invoice: ' . $invoice['invoice_no'],
                                 (float)$invoice['amount'],
                                 date('d/m/Y', strtotime($invoice['invoice_date'])),
                                );
                    fputcsv( $fp , $row );
                    
                    // Filling in breakup_details for oc_stanc_payment_breakup
                    $breakup_details['wsb_purchase'][] = array('trxn_id'     => $trxn_id, 
                                                               'trxn_for_id' => (int)$invoice['purchase_id'], 
                                                               'amount'      => (float)$invoice['amount']
                                                              );
                }
            }
            
            
            // Seller debit note - online orders
            $breakup_details['seller_debit_note'] = array();

            if(!empty($details['seller_debit_note'])){
                foreach( $details['seller_debit_note'] as $debit_note ){
                    $row = array(
                        'I',
                        $debit_note['order_no'],
                        'Debit Note: ' . $debit_note['debit_note_prefix'].$debit_note['debit_note_no'],
                        (-1) * (float)$debit_note['amount'],
                        date('d/m/Y', strtotime($debit_note['date_added'])),
                    );
                    fputcsv( $fp , $row );
                    
                    $sql = "UPDATE " . DB_PREFIX . "seller_debit_note  
                            SET trxn_done = 'BANK_REQUESTED', 
                                trxn_amount = '" . (-1) * (float)$debit_note['amount'] . "', 
                                trxn_bank = 'stanc', 
                                trxn_date_added = NOW() 
                            WHERE debit_note_id = '" . (int)$debit_note['debit_note_id'] . "'";
                    $this->_db->query($sql);
                    
                    // For storing in db
                    $breakup_details['seller_debit_note'][] = array('suborder_id' => $debit_note['suborder_id'], 
                                                                    'debit_note_id' => (int)$debit_note['debit_note_id'], 
                                                                    'debit_note_no' => (int)$debit_note['debit_note_no'], 
                                                                    'amount' => (-1) * (float)$debit_note['amount']
                                                                   );
                }
            }
            
            
            // WSB purchase return debit notes - purchased inventory
            $breakup_details['wsb_purchase_return'] = array();

            if(!empty($details['wsb_purchase_return'])){
                foreach( $details['wsb_purchase_return'] as $debit_note ){
                    
                    // Updating in oc_trxn_details
                    $trxn_id = $this->updateTrxnForPaymentRequsted('WSB_PURCHASE_RETURN', 
                                                                   (int)$debit_note['debit_note_id'], 
                                                                   (float)$debit_note['amount'], 
                                                                   'stanc');
                    if (!$trxn_id) { // something gone wrong. continue to next DN. Need to investigate manually
						continue;
					}
                    
                    // CSV 'I' row
                    $row = array(
                                 'I',
                                 'WSB-PUR-DN',
                                 'Debit Note: ' . $debit_note['debit_note_prefix'].$debit_note['debit_note_no'],
                                 (float)$debit_note['amount'],
                                 date('d/m/Y', strtotime($debit_note['date_added'])),
                                );
                    fputcsv( $fp , $row );
                    
                    // Filling in breakup_details for oc_stanc_payment_breakup
                    $breakup_details['wsb_purchase_return'][] = array('trxn_id'     => $trxn_id, 
                                                                      'trxn_for_id' => (int)$debit_note['debit_note_id'], 
                                                                      'amount'      => (float)$debit_note['amount']
                                                                     );
                }
            }
            
            
            $breakup_details['miscellaneous'] = array();

            if(!empty($details['miscellaneous'])){
                foreach( $details['miscellaneous'] as $key => $row ){
                    fputcsv( $fp , $row['csv_row'] );                    
                    // For storing in db
                    $breakup_details['miscellaneous'][] = $row['csv_row'];
                }
            }
            
            
            // Storing in DB
            $sql = "INSERT INTO `oc_stanc_payment_breakup` 
                    (`payment_csv_id`, 
                     `payment_type`, 
                     `seller_id`, 
                     `payment_ref_no`, 
                     `payee_name`, 
                     `payee_account_no`, 
                     `payee_ifsc_code`, 
                     `payee_email`, 
                     `payee_amount`, 
                     `breakup_details`) 
                    VALUES ('" . (int)$payment_csv_id . "', 
                            'SELLER_PAYMENT', 
                            '" . (int)$sid . "', 
                            '" . $this->_db->escape($payment_ref_no) . "', 
                            '" . $this->_db->escape(trim(strtoupper($details['bank_ac_holder_name']))) . "', 
                            '" . $this->_db->escape(trim($details['bank_ac_number'])) . "', 
                            '" . $this->_db->escape(trim(strtoupper($details['ifsc_code']))) . "', 
                            '" . $this->_db->escape(trim($details['email'])) . "', 
                            '" . (float)$details['total_amount'] . "', 
                            '" . $this->_db->escape(serialize($breakup_details)) . "')";
            $this->_db->query($sql);

        }
        
        // Copy the csv to Host2Host folder
        chmod($this->PYMT_SHEET_FOLDER.$filename, 0777);
        copy($this->PYMT_SHEET_FOLDER.$filename, $this->STANC_PYMT_OUT_FOLDER.$filename);
        
        // Check for ack(s) OR rej(s)
        
        // Send mail with CSV as attachment
        $this->prepareMailerObject();
        $this->_mail->addAddress('chandan.agarwal@wholesalebox.in', 'Chandan Agarwal');
        $this->_mail->addAddress('accounts@wholesalebox.in', 'Accounts WholesaleBox');
        $this->_mail->addAddress('vikas.jain@wholesalebox.in', 'Vikas Jain');
        $this->_mail->addCC('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
        $this->_mail->Subject = 'Seller Payment Sheet Uploaded on StanC H2H - ' . date('d/M/Y', time());
        $this->_mail->addAttachment($this->PYMT_SHEET_FOLDER.$filename);
        $body = "Howdy !! \n\n";
        $body .= "A seller payment sheet has been uploaded today on H2H. Find attached the csv for your information. Please login to S2B and Approve. \n\n"; 
        $body .= "Ciao !!\n";
        $body .= "Seller Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
        
        
        // Inform about Sellers whose payment could not be released due to lack of info
        $this->prepareMailerObject();
        $this->_mail->addAddress('sellers@wholesalebox.in', 'Sellers WholesaleBox');
        $this->_mail->addAddress('ashu.vyas@wholesalebox.co', 'Ashutosh Vyas');
        $this->_mail->addCC('chandan.agarwal@wholesalebox.in', 'Chandan Agarwal');
        $this->_mail->addCC('accounts@wholesalebox.in', 'Accounts WholesaleBox');
        $this->_mail->addCC('vikas.jain@wholesalebox.in', 'Vikas Jain');
        $this->_mail->addCC('madhur.bhaiya@wholesalebox.in', 'Madhur Bhaiya');
        $this->_mail->Subject = 'Sellers with Missing Bank Details - ' . date('d/M/Y', time());
        $body = "Howdy !! \n\n";
        $body .= "Following Seller(s) payments could not be released due to lack of bank details. \n"; 
        $body .= "Please update in the panel at the earliest, so that payments can be released in next cycle. \n\n";
        $i = 1;
        foreach ($this->_noinfo_sellers as $sid => $details) {
            
            $body .= $i.'. Seller: ' . $details['nickname'] . 
                            '( ' . $details['company'] . 
                            ') - Total Payment pending: Rs. ' . $details['total_amount'] . " \n";
            $i++;
        }
        $body .= "\n";
        $body .= "Ciao !!\n";
        $body .= "Seller Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
        

    }

}


$obj = new SellerPayments();        
$result = $obj->main();
