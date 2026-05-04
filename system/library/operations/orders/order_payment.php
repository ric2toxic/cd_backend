<?php

use phpDocumentor\Reflection\Types\Mixed;

/**
 * Class for Order Payment table related activities
 * Majority of functions here will be static.
 * @todo: This class to be merged with OrderEdit.
 * @Author Madhur, 2017
 */
class OrderPayment {

    /**
     * Method to apply Cash Advance against an Order.
     * @author Madhur
     */
    public static function applyCashAdvance($controller, $advance, $order_id, $order_no, $adv_payment_date, $adv_adtinal_remrks, $sales_person_id, $sales_person_name, $serialize_json = ''
    ) {

        $payable_amt = 0;
        $paid_amt = (float) $advance;
       
        //Define data array 
        $data = array();
        $data['order_id'] = (int) $order_id;
        $data['order_no'] = $order_no;
        $data['txn_status'] = 'SUCCESS';
        $data['payment_mode'] = 'CASH';
        $data['amount'] = (float) $paid_amt;
        $data['txn_date_time'] = $adv_payment_date;
        $data['date_added'] = 'NOW()';
        $data['payment_gateway'] = 'cash';
        $data['successfull'] = '1';
        $data['reference'] = $adv_adtinal_remrks;
        $data['payment_link'] = $sales_person_name;
        $data['json_format'] = $serialize_json;
        $data['user_id'] = (int) $controller->user->getId();
        $data['sales_staff_id'] = (int) $sales_person_id;

        self::insertOrderPayment($controller->db, $data); //To insert data in order payment table
        // Send SMS to Customer
        self::sendAdvanceSMS($controller, $order_id, $order_no);
    }
// close applyCashAdvance function

    /**
     * Public method to apply WSB Credit Amount in an Order.
     * @author Nishu, Feb 2019
    */
    public static function applyWsbCreditAmount($controller, $amount, $order_id, $order_no, $remarks, $serialize_json = ''
    ) {

        $payable_amt = 0;
        $paid_amt    = (float)$amount;
        
        //Define data array 
        $data = array();
        $data['order_id']        = (int) $order_id;
        $data['order_no']        = $order_no;
        $data['txn_status']      = 'SUCCESS';
        $data['payment_mode']    = 'WSB_CREDIT_DUMMY_CASH';
        $data['amount']          = (float)$paid_amt;
        $data['txn_date_time']   = date('Y-m-d H:i:s');
        $data['date_added']      = 'NOW()';
        $data['payment_gateway'] = 'wsb_credit';
        $data['successfull']     = '1';
        $data['reference']       = $remarks.'-- Payment by WSB_CREDIT';
        $data['payment_link']    = 'Payment by WSB_CREDIT';
        $data['json_format']     = $serialize_json;
        $data['user_id']         = $controller->user->getId() ?? 0;
       
        self::insertOrderPayment($controller->db, $data); //To insert data in order payment table
    }



    /**
     * Method to apply Manual Bank Transfer Advance against an Order.
     * @author Madhur
     */
    public static function applyBankTransferAdvance($controller, $response) {
        //Define data array 
        $data = array();

        $sql = "SELECT count(payment_id) total_existing_value 
                FROM  " . DB_PREFIX . "order_payment 
                WHERE order_id = '" . (int)($response['order_id']) . "'
                  AND merchant_txn_id ='" . $controller->db->escape(trim($response['payment_reff_no'])) . "' 
                  AND successfull = 1 
                  AND payment_gateway = 'bank_transfer' ";
        $query = $controller->db->query($sql);
        
        if( $query->row['total_existing_value'] ){
            return false;
        }

        if (!empty($response['bank_transfer_mode']) && $response['bank_transfer_mode'] == 'cheque_deposited') {
            $bank_transfer_mode = $response['bank_transfer_mode'];
            $txn_status = $response['bank_transfer_mode'];
        } else {
            $bank_transfer_mode = $response['bank_transfer_mode'];
            $txn_status = 'SUCCESS';
        }

        if (isset($response['payment_mode']) ){
            $data['payment_mode'] = $response['payment_mode'];
        }

        $payable_amt = 0;
        $paid_amt = (float) $response['bank_amount'];
        
        $successfull = (int)$response['successfull'] ?? 1;
      
        $data['order_id']           = (int) $response['order_id'];
        $data['merchant_txn_id']    = $response['payment_reff_no'];
        $data['order_no']           = $response['order_no'];
        $data['txn_status']         = $response['txn_status'] ?? $txn_status ?? '';
        $data['payment_mode']       = $response['payment_mode'] ?? $response['bank_name'] ?? '';
        $data['amount']             = $paid_amt;
        $data['txn_date_time']      = $response['payment_date'];
        $data['date_added']         = 'NOW()';
        $data['payment_gateway']    = $response['payment_gateway'] ?? 'bank_transfer';
        $data['successfull']        = (int)$successfull;
        $data['reference']          = $response['reference'] ?? '';
        $data['payment_link']       = $response['bank_name'] . " - " . $response['payment_reff_no'];
        $data['json_format']        = $response['serialize_response'] ?? '';
        $data['bank_transfer_mode'] = $bank_transfer_mode;
        $data['user_id']            = (int) $response['user_id'];

        //To insert data in order payment table
        $local_variable = self::insertOrderPayment($controller->db, $data); 

        //Send SMS to Customer only if transcation status is 1
        if($successfull == 1){
            self::sendAdvanceSMS($controller, $response['order_id'], $response['order_no']);
        }

        return $local_variable;
    }

// close applyBankTransferAdvance function

    /**
     * Method to update a Cash Advance payment to Bank Transfer for an Order.
     * @author Madhur
     */
    public static function updateCashAdvanceToBankTransfer($controller, $response) {
        $sql = "SELECT * FROM " . DB_PREFIX . "order_payment WHERE payment_id = " . (int) $response['payment_id'];
        $old_data = $controller->db->query($sql)->row;

        $json['old'] = $old_data;
        $json['new'] = $response;
        $new_json = serialize($json);

        $sql = "UPDATE " . DB_PREFIX . "order_payment
                        SET merchant_txn_id = '" . $controller->db->escape($response['payment_reff_no']) . "',
							txn_status 		= 'SUCCESS',
							payment_mode 	= '" . $controller->db->escape($old_data['payment_mode'] . "->" . $response['bank_name']) . "',
							amount 			= " . (float) $response['bank_amount'] . ",
							txn_date_time 	= '" . $controller->db->escape($response['payment_date']) . "',
							date_added 		= NOW(),
							payment_gateway = 'bank_transfer',
							successfull 	= '1',
							reference 		= 'Cash was collected by " . $controller->db->escape($old_data['payment_link']) . "',
							payment_link 	= '" . $controller->db->escape($old_data['payment_mode'] . "->" . $response['bank_name'] . " - " . $response['payment_reff_no']) . "',
							json_format 	= '" . $controller->db->escape($new_json) . "',
							user_id 		= '" . (int) $response['user_id'] . "'
                        WHERE payment_id = '" . (int) $response['payment_id'] . "'";
        $controller->db->query($sql);
    }

// close updateCashAdvanceToBankTransfer function

    /**
     * Method to send advance SMS to customer
     * Private static method. To be triggered only by one of the advance application methods
     * @param $controller object (required)
     * @param $order_id (int, required)
     * @author Madhur
     */
    public static function sendAdvanceSMS($controller, $order_id, $order_no) {

        // Calculating Total amount to Pay
        $order_info = OrderInfo::getOrderInfo($controller->db, $order_id, true, array('order' => array('select' => array('telephone', 'payment_code',
                            'total', 'currency_code', 'currency_value', 'live_currency_conversion_rate')))
        );

        // finding total advance collected for this order
        $total_advance = OrderInfo::getTotalAdvance($controller->db, $order_id, false);

        if ($total_advance > 0) {
            $net_payable = (float) $order_info['order']['total'] - $total_advance;

            $controller->load->language('common/sms_templates', DIR_ADMIN_LANGUAGE);
            $msg = $controller->language->get('on_advance_recieve');

            $message = sprintf($msg, $controller->currency->format($total_advance, $order_info['order']['currency_code'], $order_info['order']['live_currency_conversion_rate'], true), $order_no, $controller->currency->format($net_payable, $order_info['order']['currency_code'], $order_info['order']['currency_value'], true)
            );
            $advance_sms = new SMS($message, $order_info['order']['telephone']);
            $advance_sms->sendMessage(1, false);
        }
    }

// close sendAdvanceSMS function

    /**
     * Method that will readjust advance in Suborders
     * Private static method. Normally triggered by one of the advance application methods
     * @param $controller object (required)
     * @param $order_id (int, required)
     * @author Madhur
     */
    public static function readjustAdvanceInSuborders($db, $payment_id, $apply_advance = false) {

        // First, get total advance in this order
        $total_advance = AdvanceVoucher::getOrderPaymentByPaymentId($db, $payment_id);

        // Now, get current advance breakup for all suborders
        $advance_breakup = OrderInfo::getAdvanceBreakup($db, $payment_id);

        $freezed_advance = 0;
        $non_freezed_suborders = array();

        // Let's remove the freezed suborders and their corresponding advance values
        foreach ($advance_breakup as $suborder_id => $advance) {
            if ($advance['value'] > 0) {
                $freezed_advance += (float) $advance['value'];
            } else {
                $non_freezed_suborders[$suborder_id] = $advance;
            }
        }

        $reamining_advance = (float) $total_advance - (float) $freezed_advance;
        if ($reamining_advance > 0) {
            $splitOrder = new SplitOrder($db);
            $splitOrder->setOptions('check_subtotal_in', array_keys($non_freezed_suborders));
            $advance_factor = $splitOrder->splitBySubtotal($reamining_advance, $advance['order_id']);
        }

        foreach ($non_freezed_suborders as $suborder_id => $advance) {
            if ($reamining_advance > 0) {
                $non_freezed_suborders[$suborder_id]['value'] = !empty($advance_factor[$suborder_id]) ? (float) $advance_factor[$suborder_id] : 0;
            } else {
                $non_freezed_suborders[$suborder_id]['value'] = 0;
            }
            $non_freezed_suborders[$suborder_id]['user'] = '';
        }

//        if ($apply_advance) {
//            return self::applyAdvance($db, $order_id, $non_freezed_suborders, $check_total = false);
//        }

        return $non_freezed_suborders;

        //$nonfreezed_advance_to_split
        //if nonfreezed_advance > 0
        // split
        // apply advance
    }

    /**
     * [applyAdvance description]
     * @param  [object] $db    -   db class object
     * @param  [int] $order_id - order_id (required)
     * @param  [array] $advance - {suborder_id => {'value'=> '','user' => '',locked => ''},....}
     * @return [json string] -
     * 
     */
    public static function applyAdvance($db, $order_id, $advance, $check_total = true) {

        if (!empty($advance)) {
            if (!$check_total) {
                return self::updateAdvance($db, $advance, $order_id);
            } else if (self::validateAdvance($db, $advance, $order_id)) {
                return self::updateAdvance($db, $advance, $order_id);
            } else {
                return json_encode(array('success' => false, 'message' => 'New Advance total can not greater then the total advance.'));
            }
        }
    }

    /*
     * Function changed by Nilesh for GST requirement     */

    private static function updateAdvance($db, $advance, $order_id) {
        $total_value_per_advance = array();
        $total_advance = 0;
        $advance_voucher_ids = '';
        $advance_vouchers = AdvanceVoucher::getAdvanceVouchersByData($db, array('order_id' => $order_id));
        $payment_list = AdvanceVoucher::getPaymentRecordByOrderId($db, $order_id);

        $payment_list = array_combine(array_column($payment_list, 'payment_id'), $payment_list);
        $order_info = OrderInfo::getOrderInfo($db, $order_id, '', array('suborder' => array('select' => array('suborder_id', 'invoice_no'))));


        foreach ($payment_list as $ad_key => $ad_value) {
            $total_advance += $ad_value['amount'];
        }

        foreach ($advance_vouchers as $key => $value) {
            if ($order_info['suborder'][$value['suborder_id']]['invoice_no'] > 0) {
                continue;
            }
            $total_value_per_advance[$value['advance_voucher_id']] = ((float) $advance[$value['suborder_id']]['value'] / (float) $total_advance) * ((float) $payment_list[$value['payment_id']]['amount']);
        }
        if (!empty($total_value_per_advance)) {
            $sql = "UPDATE " . DB_PREFIX . "advance_voucher SET value = CASE ";
            foreach ($total_value_per_advance as $advance_voucher_id => $advance_data) {
                $advance_voucher_ids .= $advance_voucher_id . ',';
                $sql .= " WHEN advance_voucher_id = '" . $db->escape($advance_voucher_id) . "' THEN '" . $db->escape($advance_data) . "'";
            }
            $advance_voucher_ids = rtrim($advance_voucher_ids, ',');
            $sql .= " END ";
            $sql .= " WHERE advance_voucher_id in (" . $advance_voucher_ids . ")";
            if ($db->query($sql)) {
                return json_encode(array('success' => true));
            } else {
                return json_encode(array('success' => false));
            }
        } else {
            return json_encode(array('success' => false));
        }
    }

    private static function validateAdvance($db, $advance, $order_id) {
        $total_advance = OrderInfo::getTotalAdvance($db, $order_id);
        $new_total = 0;

        foreach ($advance as $suborder_id => $values) {
            $new_total += $values['value'];
        }

        if ($total_advance < $new_total) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Method for change bank transfer mode (using in payment history)
     * @return null
     * vikas, 2017
     */
    public static function changeBankTransferMode($controller, $response) {

        if ($controller->db->escape($response['bank_transfer_mode']) == 'cheque_success') {
            $bank_transfer_mode = $controller->db->escape($response['bank_transfer_mode']);
            $txn_status = 'SUCCESS';
            $successfull = 1;
        } else {
            $bank_transfer_mode = $controller->db->escape($response['bank_transfer_mode']);
            $txn_status = $controller->db->escape($response['bank_transfer_mode']);
            $successfull = 0;
        }

        $sql = "UPDATE " . DB_PREFIX . "order_payment
                SET txn_status      = '" . $txn_status . "',
                    successfull     = " . $successfull . ",
                    bank_transfer_mode = '" . $bank_transfer_mode . "'
                WHERE payment_id = '" . (int) $response['payment_id'] . "'";

        $controller->db->query($sql);
        if ($successfull) {
            $payment_sql = "SELECT user_id FROM " . DB_PREFIX . "order_payment WHERE payment_id = " . (int) $response['payment_id'];
            $user_id = $controller->db->query($payment_sql)->row;
            self::insertAdvanceVoucher($controller->db, array('order_id' => $response['order_id'], 'payment_id' => $response['payment_id'], 'user_id' => $user_id['user_id']));
        }
    }

    /**
     *  Insert Data to oc_order_payment
     * @param $db
     * @param array $values
     * @return bool|mixed
     * @author Nishu
     */
    public static function insertOrderPayment($db, $values) {
        if ( empty($values['order_id']) ) 
        {
            return false;
        } else {
            try {
                $db->query(" START TRANSACTION ");
                $success = 0;
                $amount = 0;
                $sql = "INSERT INTO `" . DB_PREFIX . "order_payment` ";
                $set = '';
                $is_advance = true;
                
                if (isset($values['order_id'])) {
                    $set .= ", order_id = '" . (int) $values['order_id'] . "'";
                }
                if (isset($values['merchant_txn_id'])) {
                    $set .= ", merchant_txn_id = '" . $db->escape($values['merchant_txn_id']) . "'";
                }
                if (isset($values['order_no'])) {
                    $set .= ", order_no = '" . $db->escape($values['order_no']) . "'";
                }
                if (!empty($values['txn_status'])) {
                    $set .= ", txn_status = '" . $db->escape($values['txn_status']) . "'";
                }
                if (isset($values['payment_mode'])) {
                    $set .= ", payment_mode = '" . $db->escape($values['payment_mode']) . "'";
                }
                if (isset($values['amount'])) {
                    $set .= ", amount = '" . (float) $values['amount'] . "'";
                    $amount = (float) $values['amount'];
                }
                if (empty($values['txn_date_time'])) {
                    $set .= ", txn_date_time = NULL ";
                } else {
                    $set .= ", txn_date_time = '" . $db->escape($values['txn_date_time']) . "'";
                }
                
                $set .= ", date_added = NOW() ";
                
                if (isset($values['payment_gateway'])) {
                    $set .= ", payment_gateway = '" . $db->escape($values['payment_gateway']) . "'";
                    // Dont generate Advance voucher if it is payment recovery by wsb_credit_nach
                    if ($values['payment_gateway'] == 'wsb_credit_nach') {
                        $is_advance = false;
                    }
                }
                if (isset($values['bank_transfer_mode'])) {
                    $set .= ", bank_transfer_mode = '" . $db->escape($values['bank_transfer_mode']) . "'";
                    // Dont generate Advance voucher if it is Cheque deposit only
                    if ($values['bank_transfer_mode'] == 'cheque_deposited') {
                        $is_advance = false;
                    }
                }
                if (isset($values['successfull'])) {
                    $set .= ", successfull = '" . (int) $values['successfull'] . "'";
                    $success = (int) $values['successfull'];
                }
                if (isset($values['reference'])) {
                    $set .= ", reference = '" . $db->escape($values['reference']) . "'";
                }
                if (isset($values['payment_link'])) {
                    $set .= ", payment_link = '" . $db->escape($values['payment_link']) . "'";
                }
                if (isset($values['json_format'])) {
                    $set .= ", json_format = '" . $db->escape($values['json_format']) . "'";
                }
                if (isset($values['user_id'])) {
                    $set .= ", user_id = '" . (int) $values['user_id'] . "'";
                }
                if (isset($values['sales_staff_id'])) {
                    $set .= ", sales_staff_id = '" . (int) $values['sales_staff_id'] . "'";
                }

                if (isset($values['invoice_id'])) {
                    $set .= ", invoice_id = '" . (int) $values['invoice_id'] . "'";
                }

                if (isset($values['trxn_id'])) {
                    $set .= ", trxn_id = '" . (int) $values['trxn_id'] . "'";
                }

                if (isset($values['rec_pay_tablename'])) {
                    $set .= ", rec_pay_tablename = '" . $db->escape( $values['rec_pay_tablename'] ) . "'";
                }
                if (isset($values['rec_pay_id'])) {
                    $set .= ", rec_pay_id = '" . (int) $values['rec_pay_id'] . "'";
                }
                if (isset($values['rec_pay_sub_id'])) {
                    $set .= ", rec_pay_sub_id = '" . (int) $values['rec_pay_sub_id'] . "'";
                }

                $set = trim($set, ',');

                $sql .= " SET " . $set;

                $db->query($sql);
                
                $values['payment_id'] = $db->getLastId();
                if ($success && $amount > 0 && $is_advance) {
                    self::insertAdvanceVoucher($db, $values);
                }

                $db->query(" COMMIT ");
                return $values['payment_id'];
            } catch (Exception $e) {
                $db->query(" ROLLBACK ");
                echo $e->getMessage();
            }
        }

    }

    /**
     *  Insert Data to oc_advance_voucher
     *  @param  array $data
     *  @return void
     *  @author NILESH SHARMA
     * */
    public static function insertAdvanceVoucher($db, $data) {

        $advance_voucher_prefix = '';
        // Get advance voucher prefix as per current financial year
        $financial_year = '';
        if ((int) (date('m')) <= 3) {
            $financial_year = date('Y', strtotime('-1 years')) . '-' . date('y');
        } else {
            $financial_year = date('Y') . '-' . date('y', strtotime('+1 years'));
        }
        $order_info = OrderInfo::getOrderInfo($db, $data['order_id'], '', array('suborder' =>
                    array('select' =>
                        array('suborder_id',
                            'invoice_no',
                            'order_status_id'))));

        if (!empty($order_info['suborder'])) {
            foreach ($order_info['suborder'] as $key => $value) {
                if ($value['invoice_no'] == 0 && $value['order_status_id'] != 2) {
                    $suborder_id = substr($value['suborder_id'], 12,2);
                    $prefix = 'WSB-' . $suborder_id . '-AV-' . $financial_year;

                    $query = $db->query("SELECT MAX(advance_voucher_no) AS advance_voucher_no
                                  FROM `" . DB_PREFIX . "advance_voucher`
                                  WHERE advance_voucher_prefix = '" . $db->escape($prefix) . "'");
                    if (!empty($query->row['advance_voucher_no'])) {
                        $invoice_no = (int) $query->row['advance_voucher_no'] + 1;
                    } else {
                        $invoice_no = 1;
                    }
                    $sql = "INSERT INTO `" . DB_PREFIX . "advance_voucher` ";
                    $set = '';
                    if (isset($data['order_id'])) {
                        $set .= ", order_id = '" . $db->escape($data['order_id']) . "'";
                    }
                    if (isset($value['suborder_id'])) {
                        $set .= ", suborder_id = '" . $db->escape($value['suborder_id']) . "'";
                    }
                    if (isset($data['payment_id'])) {
                        $set .= ", payment_id = '" . $data['payment_id'] . "'";
                    }

                    $set .= ", advance_voucher_prefix = '" . $prefix . "'";
                    $set .= ", advance_voucher_no = '" . $invoice_no . "'";
                    $set .= ", advance_voucher_date = '" . date("Y-m-d H:i:s") . "'";

                    if (isset($data['user_id'])) {
                        $set .= ", user_id = '" . (int) $data['user_id'] . "'";
                    }
                    $set = trim($set, ',');
                    $sql .= " SET " . $set;

                    $db->query($sql);
                }
            }
        } else {
            return false;
        }
    }

    public static function getOrderInfoIfNetPayableAmountApplicable($db, $order_id, $selector) {
        $selector['order_payment'] = array();
        $order_info = OrderInfo::getOrderInfo($db, $order_id, '', $selector);
        
        //Check if payment made via fixed amount coupon or cashback 
        
        if (!empty($order_info['order']['order_payment'])) {
            $order_payment_info = $order_info['order']['order_payment'];
            $payment_amount = 0;
            foreach ($order_payment_info as $payment_info) {
                if ($payment_info['successfull'] == 1) {
                    $payment_amount += ((float) $payment_info['amount']);
                }
            }
            
            $order_info['order']['total'] = ($order_info['order']['total'] - $payment_amount);
            
        }
        return $order_info;
    }

    public static function getOrderCategory($selector, $product_id) {
      
      $sql = "SELECT GROUP_CONCAT(pc.category_id SEPARATOR ',') AS category_id, GROUP_CONCAT(cd.name SEPARATOR ',') AS name
                FROM " . DB_PREFIX . "product_to_category pc
                INNER JOIN " . DB_PREFIX . "category_description cd ON (pc.category_id = cd.category_id)
                WHERE pc.product_id = '" . (int)$product_id . "'
                AND cd.language_id = '" . (int)$selector->config->get('config_language_id') . "' GROUP BY product_id";

        $query = $selector->db->query($sql);
        return $query->row;
       
    }

    public static function getOrderSellerNickname($selector, $seller_id) {
      
      $sql = "SELECT nickname
                FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'";

        $query = $selector->db->query($sql);
        return $query->row['nickname'];
       
    }

    /**
     * Method to apply Paytm Offline QR against an Order.
     * @author vikas, 2018
     */
    public static function applyPaytmOfflineQR($controller, $response) {
        
        $sql = "SELECT count(payment_id) total_existing_value 
                FROM  " . DB_PREFIX . "order_payment 
                WHERE order_id = '" . (int)($response['order_id']) . "'
                  AND merchant_txn_id ='" . $controller->db->escape(trim($response['paytm_payment_reff_no'])) . "' 
                  AND successfull = 1 
                  AND payment_gateway = 'paytm'";
        $query = $controller->db->query($sql);
        
        if( $query->row['total_existing_value'] ){
            return false;
        }

        $payable_amt = 0;
        $paid_amt = (float) $response['paytm_amount'];

        //Define data array 
        $data = array();
        $data['order_id'] = (int) $response['order_id'];
        $data['merchant_txn_id'] = $response['paytm_payment_reff_no'];
        $data['order_no'] = $response['order_no'];
        $data['txn_status'] = 'SUCCESS';
        $data['payment_mode'] = 'Paytm Offline QR';
        $data['amount'] = $paid_amt;
        $data['txn_date_time'] = $response['paytm_payment_date'];
        $data['date_added'] = 'NOW()';
        $data['payment_gateway'] = 'paytm';
        $data['successfull'] = '1';
        $data['reference'] = 'Paytm Offline QR Ref: ' . $response['paytm_payment_reff_no'];
        $data['json_format'] = $response['serialize_response'];
        $data['bank_transfer_mode'] = 'not_applicable';
        $data['user_id'] = (int) $response['user_id'];

        $local_variable = self::insertOrderPayment($controller->db, $data); //To insert data in order payment table
        // Send SMS to Customer
        self::sendAdvanceSMS($controller, $response['order_id'], $response['order_no']);

        return $local_variable;
    }

// close applyPaytmOfflineQR function

    /**
     * Method to update payment_cleared field in oc_order
     * @author Nishu, 2019
     */
    public static function updatePaymentClearForOrder($controller, $order_id, $payment_cleared) {
        if(!empty($order_id) && !empty($payment_cleared)){
            $sql = "
                    UPDATE
                        ".DB_PREFIX."order
                    SET
                        payment_cleared = '".$controller->db->escape($payment_cleared)."'
                    WHERE
                        order_id = ". (int)$order_id ."
                   ";
            $controller->db->query($sql);
        }
        return true;
    }

    /**
     * Method to get order_payment_id for given payment_gateway
     * @author Nishu, April 2019
     */
    public static function getPaymentIdByOrderIdAndPaymentGateway($db, $order_id, $payment_gateway) {
        $result = array();
        if(!empty($order_id) && !empty($payment_gateway)){
            $sql = "
                    SELECT
                        payment_id
                    FROM
                        ".DB_PREFIX."order_payment
                    WHERE
                        order_id            = ". (int)$order_id ."
                        AND successfull     = 1
                        AND payment_gateway = '". $db->escape($payment_gateway) ."'
                   ";
            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                $result = $qry->rows;
            }
        }
        return $result;
    }


   public static function getOrderCustomerInfo($db, $customer_id) {
      
      $sql = "SELECT c.customer_id,
                c.firstname,
                c.lastname,
                c.email,
                c.telephone,
                c.gst_number,
                c.self_order,
                c.has_website,
                c.is_dropshipper,
                c.customer_type_id,
                a.postcode,
                a.city,
                mcm.id as membership
                FROM " . DB_PREFIX . "customer c
                INNER JOIN " . DB_PREFIX . "address a ON (a.address_id = c.address_id)
                LEFT JOIN " . DB_PREFIX . "master_customer_membership mcm ON (mcm.master_id = c.master_id)
                WHERE c.customer_id = '" . (int)$customer_id . "' limit 1";

        $query = $db->query($sql);
        return $query->row;
       
    }

    /**
     * @info: Public method to get NACH attempts data regarding customer profile from order_payment table
     * @param: int $customer_id
     * @return: Nishu, 24th June 2019
     * 
    */
    public static function getNachAttemptsData($db, int $customer_id): array{
        $data = array();
        if(!empty($customer_id)){
            $sql = "
                    SELECT 
                        COUNT(DISTINCT DATE(op.txn_date_time)) AS nach_attempts,
                        COUNT(DISTINCT IF(op.successfull = 1,
                                DATE(op.txn_date_time),
                                NULL)) AS successfull_nach_attempts,
                        MAX( IF(op.successfull = 1,
                                DATE(op.txn_date_time),
                                NULL) ) AS last_successfull_nach_date,
                        MAX( IF(op.successfull = 0,
                                DATE(op.txn_date_time),
                                NULL) ) AS last_failed_nach_date
                    FROM
                        ". DB_PREFIX ."order_payment AS op
                            INNER JOIN
                        ". DB_PREFIX ."order AS o ON op.order_id = o.order_id
                            AND o.store_id IN (". WSB_STORES_ID .")
                            AND o.stock_transfer = 0
                            AND o.franchise_id = 0 
                    WHERE
                        o.customer_id = ". (int)$customer_id ."
                        AND op.payment_gateway = 'wsb_credit_nach'
                   ";
            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                $data = $qry->row;
            }
        }

        return $data;
    }

    /**
     * @info: Public method to get order ids for all payment recovered for customer_ids
     * @param: Datebase Object $db,
     * @param: int- $customer_id,
     * @return: array
     * @author: Nishu, 25th June 2019
    */
    public static function getOrderIdsForBalanceRecovered($db, $customer_id):array {
        $order_ids = array();

        if(!empty($customer_id)){

            $data = array("where" => array(
                                        "o.customer_id = ". (int)$customer_id,
                                        "o.payment_code = 'wsb_credit'",
                                        "o.total > 0" ),
                          "having" => " HAVING order_bal >= -5"
                    );

            //get Order Balance 
            $order_wise_bal = OrderAccounts::getOrderBalance($db, $data);

            if(!empty($order_wise_bal)){
                $order_ids = array_keys($order_wise_bal);
            }
        }

        return $order_ids;
    }

    /**
     * @info: public static method to get count of total Days order recovery is within 35 ,
     * @param: Database Object $db, 
     * @param: array $order_ids
     * @return: int
     * @author: Nishu, 25th June 2019 
    */
    public static function getNachPaymentOnTimeRecovered($db, array $order_ids){
        $count = 0;
        if(!empty($order_ids)){
            $sql = "
                    SELECT
                        DATEDIFF(MAX(op.txn_date_time), MAX(osub.delivered_date)) AS recovery_days
                    FROM
                        ". DB_PREFIX ."suborder AS osub
                    INNER JOIN
                        ". DB_PREFIX ."order_payment AS op ON osub.order_id = op.order_id
                        AND op.successfull = 1
                    WHERE
                        osub.order_id IN (". implode(',', $order_ids) .")
                    GROUP BY
                        osub.order_id
                    HAVING
                        recovery_days <= 35
                   ";

            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                $count = $qry->num_rows;
            }
        }
        return $count;
    }
}

// close OrderPayment class
?>
