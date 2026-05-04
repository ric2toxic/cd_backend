<?php
require_once(DIR_SYSTEM.'library/operations/payment_gateway/payment_gateway_base.php');

class WsbCreditPayment extends PaymentGatewayBase {

	public function __construct($registry) {
		parent::__construct($registry);
        $this->paymentgateway = 'wsb_credit';
	}

	/**
	 *	doAction
	 *	@info doAction always call because this method know that which method to call  
	 *	@param array $data
	 *	@return data
	 */
	public function doAction($data) {
		if (!empty($data['method'])) { 
			$method_name = ucfirst($data['method']).'('.$data.')';
			if(function_exists($method_name)) { // Here we can check that method is exit or not
            	$post_action = $this->$method_name;
            	$this->getPostAction($post_action);
            } else {
            	throw new Exception("Invalid Method Name in OperationsFactory->getInstance.");
            }
        } else {
            throw new Exception("Empity Method Name in OperationsFactory->getInstance.");
        }
	}

	/**
	 *	getPostAction
	 *	@info getPostAction always call in last and this will decide that which class to call 
	 *	@param 	array $data
	 *	@return data
	 */
	public function getPostAction($data) {
		return new $data();
	}

	/**
     * Public method to get wsb_credit details for given customer_ids
     * @param : $customer_id
     * @author: Nishu, Feb 2019
	*/
	public function getWsbCreditDetailByCustomerIds($customer_ids, $selector = array()){
        if (empty($customer_ids)) return array();
        
		if(is_array($customer_ids)){
			$customer_ids = implode(',', $customer_ids);
		}
		$credit_details = array();
		if(empty($selector)){
			$selector = array(
							'credit_limit', 
							'status', 
							'nach_schedule_crontab', 
							'days_before_nach_start', 
							'schedule_days_for_nach', 
							'auto_nach_enabled'
						);
		}
		$select_fields = implode(", ", $selector);
		
		$sql = "
                SELECT 
                	customer_id,
                 	". $select_fields ."
                FROM
                	".DB_PREFIX."customer_wsb_credit AS cwc
                WHERE
                	customer_id IN (".$customer_ids.")
		       ";
		$qry = $this->db->query($sql);
		
		if($qry->num_rows > 0){
			$credit_details = array_combine(
								array_column($qry->rows, 'customer_id'),
				                $qry->rows);	
		}
		return $credit_details;
	}

	/*
	 * @method: createTransactionLog- create neogrowth transaction log entry
	 * @params: data (key => value) transaction data
	 * @return: log id
	 * @author: Devendra, July 2018
	*/
	public function createTransactionLog(array $data): int {
		$fields = array('url', 'request','order_id', 'order_no', 'request_type', 'transaction_id', 'transaction_amount', 'buyer_registration_number');
		$sql = "INSERT INTO 
					" . DB_PREFIX . "transaction_logs 
				SET 
					type          = 'WSB_CREDIT', 
					status        = 'NEW',
					date_added    = NOW(),
					date_modified = NOW() ";
		foreach ($fields as $key) {
			if (isset($data[$key])) {
				$sql .= ", " . $key . "='" . $this->db->escape($data[$key]) . "' ";
			}
		}

		$this->db->query($sql);
		$log_id = $this->db->getLastId();
		
		return (int)$log_id;
	}
		
	/*
	 * @method: getTransactionLogUsingOrderNo- get neogrowth transactions happened for an order
	 * @params: order number, 
	 * @params: request_type (optional) possible values: ‘Purchased’, ‘Delivered’, ‘Cancelled’, ‘Return’ , 'GetOrderStatus', 'GetOTBL'
	 * @author: Nishu, Jan 2019
	 */
	public function getTransactionLogUsingOrderNo(string $order_no, string $request_type=''): array {
		$sql = "
		        SELECT  
		        	log_id, 
		        	type, 
		        	request_type, 
		        	transaction_amount, 
		        	date_added, 
		        	date_modified, 
		        	transaction_id, 
					buyer_registration_number, 
					status, message, 
					response, 
					order_no 
				FROM 
					" . DB_PREFIX . "transaction_logs 
				WHERE 
					order_no='" . $this->db->escape($order_no) ."' 
		            AND type = 'WSB_CREDIT' ";
		
		if (!empty($request_type)) {
			$sql .= " AND request_type='" . $this->db->escape($request_type) . "'";
		}
	
		$result = $this->db->query($sql);
		if ($result->num_rows) {
			return $result->rows;
		}
		
		return array();
	}

	/**
     * Public method to get active WSB CREDIT limit for given customer ids
     * @param: array $customer_ids
     * @return: Array 
     * @author: Nishu, Jan 2019
	*/
	public function getActiveWsbCreditLimitByCustomerIds(array $customer_ids){
		$credit_limit = array();
		
		// Sanitizing $customer_ids input
		$customer_ids = array_unique(array_filter(array_map('intval', $customer_ids)));
		
		if (empty($customer_ids)) return array();

		$sql = "
	            SELECT 
	              customer_id,
	               credit_limit
	            FROM
	              ".DB_PREFIX."customer_wsb_credit AS cwc
	            WHERE
				  customer_id IN (".implode(',', $customer_ids).")
	                	AND status = 'ENABLED'
			       ";
		$qry = $this->db->query($sql);
			
		if($qry->num_rows > 0){
			$credit_limit = array_combine(
								array_column($qry->rows, 'customer_id'),
					            array_column($qry->rows, 'credit_limit'));	
		}
		
		return $credit_limit;
	}

	/**
     * Public method to get WSB Credit balance used
     * @param: integer $customer_id, int $order_id (i.e. WSB credit balance used against that specific order)
     *         Important Note:- If any entry in order_payment table for payment_gateway='wsb_credit' is not exist then order_bal (only negaive value) will be treated as used WSB credit balance for given order id
     * @return: float $available_bal
     * @author: Nishu, Feb 2019
	*/
	public function getUsedWsbCreditBalanceForOrderId(int $customer_id, int $order_id){
		
		$sql = "
				SELECT 
				    SUM(IF(tbl.wsb_credit_amount > 0,
				        LEAST(tbl.wsb_credit_amount,
				                tbl.order_bal),
				        tbl.order_bal) ) AS used_wsb_credit
				FROM
				    (SELECT 
			        	ABS(LEAST( ROUND((- COALESCE(o.total, 0) + COALESCE(cn.cn_amount, 0) - COALESCE(cn.cod_failed_penalty, 0) + COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1
			                AND (op.payment_gateway = 'cashback'
			                OR op.payment_gateway = 'coupon'), op.amount, 0)), 0) + COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1
			                AND op.payment_gateway != 'cashback'
			                AND op.payment_gateway != 'coupon'
			                AND op.payment_gateway != 'wsb_credit'
			                AND op.txn_status != 'cheque_deposited', op.amount, 0)), 0) + COALESCE(SUM(IF(op.amount < 0 AND op.successfull = 1
			                AND op.payment_gateway != 'cashback'
			                AND op.payment_gateway != 'coupon'
			                AND op.payment_gateway != 'wsb_credit', op.amount, 0)), 0) 
			                - COALESCE(cn.less_cash_discount, 0)
			                + COALESCE(cn.other_charges, 0)
			                ), 2), 0)) AS order_bal,
                        COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1 
                                        AND op.payment_gateway IN ('wsb_credit', 'wsb_credit_nach'), 
                                        IF(op.payment_gateway = 'wsb_credit', op.amount, (-1)*op.amount), 
                                        0)), 0) AS wsb_credit_amount 
				    FROM
				        ". DB_PREFIX ."order o
				    
				    LEFT JOIN ". DB_PREFIX ."order_payment AS op ON op.order_id = o.order_id
				    LEFT JOIN (SELECT 
                                 COALESCE(SUM(credit_note_amount), 0) AS cn_amount,
				                 COALESCE(SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
				                 COALESCE(SUM(less_cash_discount), 0) AS less_cash_discount,
                                 COALESCE(SUM(other_charges), 0) AS other_charges, 
				                 order_id
				               FROM ". DB_PREFIX ."credit_note
				               WHERE credit_note_status = 1
				               GROUP BY order_id) AS cn ON cn.order_id = o.order_id
				    WHERE
				        o.payment_code = 'wsb_credit'
				            AND o.customer_id = ". (int)$customer_id  . " 
				            AND o.order_id         = ". (int)$order_id . "
				            AND o.currency_code = 'INR'
				            AND o.store_id IN (0 , 2, 9)
				            AND o.stock_transfer = 0
				            AND EXISTS(SELECT 1 FROM ". DB_PREFIX ."suborder AS osub WHERE osub.order_id = o.order_id AND osub.order_status_id > 0 )
				            AND o.franchise_id = 0 
				    GROUP BY o.order_id) AS tbl
		       ";

		$q = $this->db->query($sql);
		$used_credit_bal = $q->row['used_wsb_credit'] ?? 0;

		return round($used_credit_bal, 2);
	}

	/**
     * Public method to get WSB Credit balance used
     * @param: integer $customer_id
     *         Important Note:- If any entry in order_payment table for payment_gateway='wsb_credit' is not exist then order_bal (only negaive value) will be treated as used WSB credit balance
     * @return: float $available_bal
     * @author: Nishu, Feb 2019
	*/
	public function getUsedWsbCreditBalanceForAllOrder(int $customer_id, int $not_order_id =0){
		
		$whr = "";
		if(!empty($not_order_id)){
			$whr = " AND o.order_id != ". (int)$not_order_id;
		}
		
		$sql = "
				SELECT 
				    SUM(IF(tbl.wsb_credit_amount > 0,
				        LEAST(tbl.wsb_credit_amount,
				                tbl.order_bal),
				        tbl.order_bal) ) AS used_wsb_credit
				FROM
				    (SELECT 
				        ABS(LEAST( ROUND((- COALESCE(o.total, 0) + COALESCE(cn.cn_amount, 0) - COALESCE(cn.cod_failed_penalty, 0) + COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1
			                AND (op.payment_gateway = 'cashback'
			                OR op.payment_gateway = 'coupon'), op.amount, 0)), 0) + COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1
			                AND op.payment_gateway != 'cashback'
			                AND op.payment_gateway != 'coupon'
			                AND op.payment_gateway != 'wsb_credit'
			                AND op.txn_status != 'cheque_deposited', op.amount, 0)), 0) + COALESCE(SUM(IF(op.amount < 0 AND op.successfull = 1
			                AND op.payment_gateway != 'cashback'
			                AND op.payment_gateway != 'coupon'
			                AND op.payment_gateway != 'wsb_credit', op.amount, 0)), 0) 
			                - COALESCE(cn.less_cash_discount, 0)
			                + COALESCE(cn.other_charges, 0)
			                ), 2), 0)) AS order_bal,
                        COALESCE(SUM(IF(op.amount > 0 AND op.successfull = 1 
                                        AND op.payment_gateway IN ('wsb_credit', 'wsb_credit_nach'), 
                                        IF(op.payment_gateway = 'wsb_credit', op.amount, (-1)*op.amount), 
                                        0)), 0) AS wsb_credit_amount 
				    FROM
				        ". DB_PREFIX ."order o
				    
				    LEFT JOIN ". DB_PREFIX ."order_payment AS op ON op.order_id = o.order_id
				    LEFT JOIN (SELECT 
				                 COALESCE(SUM(credit_note_amount), 0) AS cn_amount,
				                 COALESCE(SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
				                 COALESCE(SUM(less_cash_discount), 0) AS less_cash_discount, 
                                 COALESCE(SUM(other_charges), 0) AS other_charges, 
				                 order_id
                               FROM ". DB_PREFIX ."credit_note
				               WHERE credit_note_status = 1
				               GROUP BY order_id) AS cn ON cn.order_id = o.order_id
				    WHERE
				        o.payment_code = 'wsb_credit'
				            AND o.customer_id = ". (int)$customer_id  . $whr ." 
				            AND o.currency_code = 'INR'
				            AND o.store_id IN (0 , 2, 9)
				            AND o.stock_transfer = 0
				            AND EXISTS(SELECT 1 FROM ". DB_PREFIX ."suborder AS osub WHERE osub.order_id = o.order_id AND osub.order_status_id > 0 )
				            AND o.franchise_id = 0 
				    GROUP BY o.order_id) AS tbl
		       ";
		     
		$query = $this->db->query($sql);

		$used_credit_bal = 0;
		if($query->num_rows > 0){
			$used_credit_bal = round($query->row['used_wsb_credit'], 2);
		}

		return $used_credit_bal;
	}

	/**
     * Public method to get avaiable WSB_CREDIT balance for given customer_id
	*/
	public function getAvaiableCreditBalance(int $customer_id, $not_order_id = 0){
		$credit_bal = 0;

		//Customer id wise array for credit limit
		$credit_limit = $this->getActiveWsbCreditLimitByCustomerIds(array($customer_id));

		//Active credit limit for given customer id 
		$total_active_credit_limit = $credit_limit[$customer_id] ?? 0;
		
		if($total_active_credit_limit > 0){

			//get used WSB credit balance for all orders excluding order id
			$used_credit_bal = $this->getUsedWsbCreditBalanceForAllOrder($customer_id, $not_order_id);

			$credit_bal = (float)($total_active_credit_limit - $used_credit_bal); 
			$credit_bal = ROUND($credit_bal, 2);
		}

		return $credit_bal;
	}

	/*
	 * @method: insertWsbCreditPaymentDetailsIntoDb- insert neogrowth payment into db
	 * @params: order id
	 * @author: Devendra, July 2018
	 */
	public function insertWsbCreditPaymentDetailsIntoDb(array $data) {
		
		$data['txn_date_time']      = date("Y-m-d H:i:s");
		$data['txn_status']         = 'SUCCESS';
		$data['date_added']         = 'NOW()';
		$data['payment_gateway']    = $data['payment_gateway'] ?? 'wsb_credit';
		$data['successfull']        = '1';
		$data['reference']          = 'Payment by WSB_CREDIT';
		$data['payment_link']       = 'Payment by WSB_CREDIT';
		$data['json_format']        = serialize($data);
		$data['user_id']            = '0';
    
		$valid_insertion = 1;
		// check if row is already there with same information
		if($data['amount'] > 0) {
			$payment_data = $this->__getPaymentData((string)$data['order_no'], $data['payment_gateway']);
			if(!empty($payment_data)) {
				$valid_insertion = 0;
			}
		}

		if($valid_insertion && $data['order_id'] != 0) {
			OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
		}
		
		return true;
	}
		
	/*
	 * @method: getSubOrderWiseAmount- get neogrowth amount distributed among suborders 
	 * @params: order id, order payment data
	 * @return: suborder wise amount
	 * @author: Devendra, July 2018
	 */
	public function getSubOrderWiseAmount(int $order_id, array $payment_data): array {
		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) return array();
		
		$purchase_data = $purchase_records[0];
		
		$advance_vouchers = AdvanceVoucherLib::getActiveAdvanceVouchersWithPayment($this->db, $order_id, $payment_data['payment_id']);
		
		$suborder_wise_amount = array();
		
		foreach ($advance_vouchers as $advance_voucher) {
			$suborder_wise_amount[$advance_voucher['suborder_id']] = $advance_voucher['value'];
		}
		
		return $suborder_wise_amount;
	}
	
	/* private method to get payment data for wsb credit payment */
	private function __getPaymentData(string $order_no, string $payment_gateway): array {
		$sql = "SELECT 
					payment_id 
				FROM 
					". DB_PREFIX . "order_payment 
				WHERE 
					order_no            = '". $this->db->escape($order_no) ."' 
					AND successfull     = '1' 
					AND payment_gateway = '".$this->db->escape($payment_gateway)."'";
		$query_result = $this->db->query($sql);
		if($query_result->num_rows) {
			return $query_result->row;
		}
		
		return array();
	}

	/**
     * Public method to update wsb_credit details for given customer_id from customer_credits tab
     * @param : $data Array
     * @author:Nishu, 2019
	*/
	public function updateCreditDetails($data){
		if(!empty($data['customer_id'])){
			//Set Data to add into admin_change_log
            $admin_change_data = array();
            $admin_change_data['table_id'] = (int)$data['customer_id'];
            
			$select_sql = "
                            SELECT
                            	status,
                            	credit_limit,
                            	nach_schedule_crontab,
                            	days_before_nach_start,
                 				schedule_days_for_nach,
                 				auto_nach_enabled
                            FROM
                            	".DB_PREFIX."customer_wsb_credit
                            WHERE 
                            	customer_id = ". (int)$data['customer_id'] ."
			              ";
			$result = $this->db->query($select_sql);
			if($result->num_rows > 0){

				$is_updatable = 0;
				$old_credit_status = $result->row['status'] ?? '';
				$credit_status     = $data['status'] ?? '';

				$old_credit_limit = $result->row['credit_limit'] ?? '';
				$credit_limit     = $data['credit_limit'] ?? '';

				$old_nach_schedule_crontab  = $result->row['nach_schedule_crontab'] ?? '';
				$nach_schedule_crontab      = $data['nach_schedule_crontab'] ?? '';

				$old_days_before_nach_start = $result->row['days_before_nach_start'] ?? '';
				$days_before_nach_start     = $data['days_before_nach_start'] ?? '';

				$old_schedule_days_for_nach = $result->row['schedule_days_for_nach'] ?? '';
				$schedule_days_for_nach     = $data['schedule_days_for_nach'] ?? '';

				$old_auto_nach_enabled      = $result->row['auto_nach_enabled'];
				$auto_nach_enabled          = $data['auto_nach_enabled'] ?? 1;
        		
				if(
					$credit_status <> $old_credit_status || 
					$credit_limit <> $old_credit_limit || 
					$nach_schedule_crontab <> $old_nach_schedule_crontab || 
					$days_before_nach_start <> $old_days_before_nach_start || 
					$schedule_days_for_nach <> $old_schedule_days_for_nach || 
					$auto_nach_enabled <> $old_auto_nach_enabled
			    ){
					$is_updatable = 1;
				}
				
				if($is_updatable == 1){

					$log_data = array();
					$log_data['customer_id']            = (int)$data['customer_id'];
					$log_data['status']                 = $data['status'];
					$log_data['credit_limit']           = (float)$data['credit_limit'];
					$log_data['nach_schedule_crontab']  = $data['nach_schedule_crontab'];
					$log_data['days_before_nach_start'] = (int)$data['days_before_nach_start'];
					$log_data['schedule_days_for_nach'] = (int)$data['schedule_days_for_nach'];
					$log_data['auto_nach_enabled']      = (int)$data['auto_nach_enabled'];
					$log_data['comment']                = $data['comment'] ?? '';
					
					$this->changeLogIntoHistory($log_data);

					$update_sql = "
	                                UPDATE
	                                	".DB_PREFIX."customer_wsb_credit
	                                SET
	                                	status = '".$this->db->escape($data['status'])."',
	                                	credit_limit = '".(float)$data['credit_limit']."',
	                                	nach_schedule_crontab = '".$this->db->escape($data['nach_schedule_crontab'])."',
	                                	days_before_nach_start = '".(int)$data['days_before_nach_start']."',
	                                	schedule_days_for_nach = '".(int)$data['schedule_days_for_nach']."',
	                                	auto_nach_enabled = '".(int)$data['auto_nach_enabled']."'
	                                WHERE
	                                	customer_id = ". (int)$data['customer_id'];
					$this->db->query($update_sql);
				}
				
			}else{

				$log_data = array();
				$log_data['customer_id']            = (int)$data['customer_id'];
				$log_data['status']                 = $data['status'];
				$log_data['credit_limit']           = (float)$data['credit_limit'];
				$log_data['nach_schedule_crontab']  = $data['nach_schedule_crontab'];
				$log_data['days_before_nach_start'] = (int)$data['days_before_nach_start'];
				$log_data['schedule_days_for_nach'] = (int)$data['schedule_days_for_nach'];
				$log_data['auto_nach_enabled']      = (int)$data['auto_nach_enabled'];
				$log_data['comment']                = $data['comment'] ?? '';
				$this->changeLogIntoHistory($log_data);

				$insert_sql = "
                                INSERT INTO
                                	".DB_PREFIX."customer_wsb_credit
                                SET
                                	customer_id            = ".(int)$data['customer_id'].",
                                	status                 = '".$this->db->escape($data['status'])."',
                                	credit_limit           = '".(float)$data['credit_limit']."',
                                	nach_schedule_crontab  = '".$this->db->escape($data['nach_schedule_crontab'])."',
                                	days_before_nach_start = '".(int)$data['days_before_nach_start']."',
	                                schedule_days_for_nach = '".(int)$data['schedule_days_for_nach']."',
	                                auto_nach_enabled      = ". (int)$data['auto_nach_enabled'] ."
				              ";
				$this->db->query($insert_sql);
			}
		}
	}

	/**
	 * Public method to log admin change log
	 * @author : Nishu
	*/
	public function changeLogIntoHistory($data){
		/////////////// Insert a row in customer change log/////////////////////
          
        $data['customer_id']             = $data['customer_id'] ?? 0;
        $data['status']                  = $data['status'] ?? 'DISABLED';
        $data['credit_limit']            = $data['credit_limit'] ?? '0.00';
		$data['nach_schedule_crontab']   = $data['nach_schedule_crontab'] ?? '30 11 * * *';
		$data['days_before_nach_start']  = $data['days_before_nach_start'] ?? 4;
		$data['schedule_days_for_nach']  = $data['schedule_days_for_nach'] ?? 20;
        $data['auto_nach_enabled']       = $data['auto_nach_enabled'] ?? 1;
        $data['comment']                 = $data['comment'] ?? '';
        $data['date_added']              = date('Y-m-d H:i:s');
        $data['user_id']                 = $this->user->getId() ?? 0;
        $data['username']                = $this->user->getUserName()["name"] ?? '';
        $data['user_agent']              = $_SERVER['HTTP_USER_AGENT'];
        $data['ip_address']              = $_SERVER['REMOTE_ADDR'];
        
        $sql = "
        		INSERT INTO
        			".DB_PREFIX."customer_wsb_credit_log
        		SET
        			customer_id = ".(int)$data['customer_id'].",
        			status      = '".$this->db->escape(trim($data['status']))."',
        			credit_limit= '".(float)$data['credit_limit']."',
        			nach_schedule_crontab = '".$this->db->escape(trim($data['nach_schedule_crontab']))."',
        			days_before_nach_start = ".(int)$data['days_before_nach_start'].",
        			schedule_days_for_nach = ".(int)$data['schedule_days_for_nach'].",
        			auto_nach_enabled      = ".(int)$data['auto_nach_enabled'].",
        			comment                = '".$this->db->escape(trim($data['comment']))."',
        			date_added             = '".$this->db->escape(trim($data['date_added']))."',
        			user_id                = ".(int)$data['user_id'].",
        			username               = '".$this->db->escape(trim($data['username']))."',
        			user_agent             = '".$this->db->escape(trim($data['user_agent']))."',
        			ip_address             = '".$this->db->escape(trim($data['ip_address']))."'
               ";
        $this->db->query($sql);
	}

	/**
     * @info : Public method to update status for customer_credit
     * @param: array
     * @author: Nishu, April 2019
	*/
	public function updateWsbCreditStatus($data){
		if(!empty($data)){

			$select_sql = "
                            SELECT
                            	status,
                            	credit_limit,
                            	nach_schedule_crontab,
                            	days_before_nach_start,
                            	schedule_days_for_nach,
                            	auto_nach_enabled
                            FROM
                            	".DB_PREFIX."customer_wsb_credit
                            WHERE 
                            	customer_id = ". (int)$data['customer_id'] ."
			              ";
			$result = $this->db->query($select_sql);
			if($result->num_rows > 0){
				$old_credit_status = $result->row['status'] ?? '';
				$credit_status     = $data['status'] ?? '';

				if($credit_status <> $old_credit_status){
		            
					//Set Data to add into admin_change_log
					$log_data = array();
					$log_data['customer_id']            = (int)$data['customer_id'];
					$log_data['status']                 = $credit_status;
					$log_data['credit_limit']           = (float)$result->row['credit_limit'];
					$log_data['nach_schedule_crontab']  = $result->row['nach_schedule_crontab'];
					$log_data['days_before_nach_start'] = (int)$result->row['days_before_nach_start'];
					$log_data['schedule_days_for_nach'] = (int)$result->row['schedule_days_for_nach'];
					$log_data['auto_nach_enabled']      = (int)$result->row['auto_nach_enabled'];
					$log_data['comment']                = $data['comment'] ?? '';
					$this->changeLogIntoHistory($log_data);

                	$update_sql = "
	                                UPDATE
	                                	".DB_PREFIX."customer_wsb_credit
	                                SET
	                                	status = '".$this->db->escape($data['status'])."'
	                                WHERE
	                                	customer_id = ". (int)$data['customer_id'];
					$this->db->query($update_sql);
				}
			}	
		}
	}

	/**
     * Public method to get NACH Schedule's log history
     * @author : Nishu
    */
    public function getWsbCreditHistory($customer_id){
        $data = array();
        $sql = "
                SELECT 
                    log_id,
                    customer_id,
                    status,
                    credit_limit,
                    nach_schedule_crontab,
                    days_before_nach_start,
                    schedule_days_for_nach,
                    auto_nach_enabled,
                    comment,
                    date_added,
                    user_id,
                    username
                FROM
                    ".DB_PREFIX."customer_wsb_credit_log
                WHERE
                    customer_id = ". (int)$customer_id ."
                ORDER BY
                    log_id DESC
               ";
        
        $qry = $this->db->query($sql);
        if($qry->num_rows > 0){
            $data = $qry->rows;
        }

        return $data;
    }

}
