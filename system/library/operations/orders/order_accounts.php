<?php

/**
 * Main Class for acccounts related calcaulations and data processing for orders
 * 
 * @Author Nishu, June 2019
 */
class OrderAccounts {

    private $_date_stmt_available  = '2018-04-01';
    private $_payment_codes = array(
                                    'cod'          => array('cod'),
                                    'wsb_credit'   => array('wsb_credit'),
                                    'other_credit' => array('credit', 'wsb_credit_card', 'lazypay', 'udaan_credit'),
                                    'prepaid'      => array('bank_transfer', 'ccavenue', 'citrus', 'paytabs', 'paytm', 'prepaid', 'razorpay', 'upi')
                                   ); 

    /**
     * @info: Public method to get only order_id(s) after appling filter(s)
     * @param: Basic filters Like: 
     *         $filter_data array format
     *         keys-->
     *            customer_id -  (Required)      : integer
     *            filter_order_id(optional)      : string (Can be comma sperated string for multiple order_ids)
     *            filter_order_no(optional)      : string
     *            filter_payment_method(optional): array (multiple payment_methods)
     *            filter_invoice_no       (optional): Sub-Order Invoice No.
     *            filter_ordered_date_from(optional): Order Placed Date(date_added from oc_order table)
     *            filter_ordered_date_to  (optional): Order Placed Date(date_added from oc_order table)
     *            filter_invoice_date_from(optional): Sub-Order Invoice Date
     *            filter_invoice_date_to  (optional): Sub-Order Invoice Date
     * example $filter_data 
     *            $filter_data = array(
     *                            'customer_id'              => <customer_id>,
     *                            'filter_order_id'          => '<order_id1>,<order_id2>,<order_id3>',
     *                            'filter_order_no'          => '<order_no>',
     *                            'filter_payment_method'    => array('wsb_credit', 'cod'),
     *                            'filter_invoice_no'        => <invoice_no>,
     *                            'filter_ordered_date_from' => <ordered_date_from>,
     *                            'filter_ordered_date_to'   => <ordered_date_to>,
     *                            'filter_invoice_date_from' => <invoice_date_from>,
     *                            'filter_invoice_date_to'   => <invoice_date_to>
     *                           );
     * 
    */
    public function getOrderIdsForOrderAccountCalculation($db, array $filter_data){
        
        $order_ids = array();
        // Trim (data sanitization) of all values
        //$filter_data = array_map('trim', $filter_data);
        
        if(!empty($filter_data)){
            $sql = "
                    SELECT DISTINCT
                        o.order_id
                    FROM
                        oc_order o
                            INNER JOIN
                        oc_suborder AS osub ON o.order_id = osub.order_id
                            AND osub.order_status_id > 0
                            AND osub.order_status_id != 2
                    WHERE
                        o.currency_code = 'INR'
                            AND o.store_id IN ('. WSB_STORES_ID .')
                            AND o.stock_transfer = 0
                            AND o.franchise_id = 0 
                            AND o.date_added >= '".$this->_date_stmt_available." 00:00:00'
                   ";
            if(!empty($filter_data['customer_id'])) {
                $sql .= " AND o.customer_id = ".(int)$filter_data['customer_id']." ";
            }

            if(!empty($filter_data['filter_payment_method'])) {
                $filter_payment_methods = array();
                if(is_array($filter_data['filter_payment_method'])){
                    $filter_payment_methods = $filter_data['filter_payment_method'];
                }else{
                    $filter_payment_methods = explode(',', $filter_data['filter_payment_method']);
                }
                $payment_codes = array();
                foreach ($filter_payment_methods as $filter_payment_method) {
                    if( !empty($this->_payment_codes[$filter_payment_method]) ){
                        $payment_codes = array_merge($payment_codes, $this->_payment_codes[$filter_payment_method]);
                    }
                }
                if(!empty($payment_codes)){
                    $payment_codes = implode("','", $payment_codes );
                    $sql .= " AND o.payment_code IN ('".$payment_codes."') ";
                }
            }
            
            $filter_data['filter_order_id'] = array_unique(array_filter(array_map('intval', $filter_data['filter_order_id'] ?? array())));
            if(!empty($filter_data['filter_order_id'])) {
                $sql .= " AND o.order_id IN (".$filter_data['filter_order_id'].") ";
            }

            if(!empty($filter_data['beyond_order_id'])) {
                $sql .= " AND o.order_id < ". (int)$filter_data['beyond_order_id'];
            }

            if(!empty($filter_data['filter_order_no'])) {
                $sql .= " AND o.order_no LIKE '%". $db->escape(trim($filter_data['filter_order_no'])) ."%' ";
            }

            if(!empty($filter_data['filter_ordered_date_from'])) {
                $filter_ordered_date_from = date('Y-m-d', strtotime($filter_data['filter_ordered_date_from']));
                $sql .= " AND o.date_added >= '". $db->escape(trim($filter_ordered_date_from)) ." 00:00:00' ";
            }

            if(!empty($filter_data['filter_ordered_date_to'])) {
                $filter_ordered_date_to = date('Y-m-d', strtotime($filter_data['filter_ordered_date_to']));
                $sql .= " AND o.date_added <= '". $db->escape(trim($filter_ordered_date_to)) ." 23:59:59' ";
            }

            if(!empty($filter_data['filter_invoice_no'])) {
                $sql .= " AND CONCAT(osub.invoice_prefix, osub.invoice_no) LIKE '%". $db->escape(trim($filter_data['filter_invoice_no'])) . "%' ";
            }

            if(!empty($filter_data['filter_invoice_date_from'])) {
                $filter_invoice_date_from = date('Y-m-d', strtotime($filter_data['filter_invoice_date_from']));
                $sql .= " AND osub.invoice_date >= '". $db->escape(trim($filter_invoice_date_from)) ." 00:00:00' ";
            }

            if(!empty($filter_data['filter_invoice_date_to'])) {
                $filter_invoice_date_to = date('Y-m-d', strtotime($filter_data['filter_invoice_date_to']));
                $sql .= " AND osub.invoice_date <= '". $db->escape(trim($filter_invoice_date_to) ) ." 23:59:59' ";
            }
            $sql .= " ORDER BY o.order_id DESC ";
            if(isset($filter_data['limit'])) {
                $sql .= " LIMIT 0, " . (int)$filter_data['limit'];
            }

            $qry = $db->query($sql);

            if($qry->num_rows > 0){
                $data = $qry->rows;
                $order_ids = array_column($data, 'order_id');
            }
        }
        return $order_ids;
    }

    /**
     * @info: Public method to get customer level account details like : 
     *        total_no_of_orders, total_debits, total_credits and total_balance
     * @param : $db object
     * @param : array $order_ids
     * @return: Array keys -->
     *                $selector + keys('total_debits' and 'total_credits')
     *
     * @author: Nishu, June 2019
    */
    public function getTotalDebitsAndTotalCreditsByOrderIds($db, array $order_ids, array $selector, string $group_by): array{
        $data = array();
        
        //Top ensure that invalid order_ids in the array are removed
        $order_ids = array_unique(array_filter(array_map('intval',$order_ids)));
        
        if(!empty($order_ids) && !empty($selector)){
        
            $sql = "
                    SELECT 
                        ". implode(',', $selector) .",
                        
                        ROUND(
                            (
                            SUM(osub.suborder_total) + 
                            SUM(COALESCE(cn.cod_failed_penalty, 0)) + 
                            SUM(COALESCE(cn.less_cash_discount, 0)) + 
                            SUM(COALESCE(op.refund_amount, 0)) +
                            SUM(IF(cn.other_charges < 0, ABS(cn.other_charges), 0))
                            ),
                            2) AS total_debits,
                        ROUND(
                            (
                            SUM(COALESCE(cn.cn_amount, 0)) + 
                            SUM(COALESCE(op.amount_received, 0)) +
                            SUM(IF(cn.other_charges > 0, cn.other_charges, 0))
                            ),
                            2) AS total_credits
                    FROM
                        oc_order o
                        
                        INNER JOIN
                    (SELECT 
                        order_id, SUM(total) AS suborder_total
                    FROM
                        oc_suborder
                    WHERE
                        order_status_id > 0
                            AND order_status_id != 2
                    GROUP BY order_id) AS osub ON o.order_id = osub.order_id
                        LEFT JOIN
                    (SELECT 
                        order_id,
                            ABS(COALESCE(SUM(IF(amount < 0
                                AND payment_gateway != 'cashback'
                                AND payment_gateway != 'coupon', amount, 0)), 0)) AS refund_amount,
                            ABS(COALESCE(SUM(IF(amount > 0, amount, 0)), 0)) AS amount_received
                    FROM
                        oc_order_payment
                    WHERE
                        successfull = 1
                            AND payment_gateway != 'wsb_credit'
                    GROUP BY order_id) AS op ON op.order_id = o.order_id
                        LEFT JOIN
                    (SELECT 
                        COALESCE(SUM(credit_note_amount), 0) AS cn_amount,
                            COALESCE(SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
                            COALESCE(SUM(less_cash_discount), 0) AS less_cash_discount,
                            COALESCE(SUM(other_charges), 0) AS other_charges,
                            order_id
                    FROM
                        oc_credit_note
                    WHERE
                        credit_note_status = 1
                    GROUP BY order_id) AS cn ON cn.order_id = o.order_id
                WHERE
                    o.currency_code = 'INR'
                        AND o.store_id IN ('. WSB_STORES_ID .')
                        AND o.stock_transfer = 0
                        AND o.franchise_id = 0 
                        AND o.date_added >= '".$this->_date_stmt_available." 00:00:00'
                    ";
                    
            $sql .= " AND o.order_id IN (". implode(',', $order_ids) .") ";

            if( empty($group_by) ){
                $sql .= " GROUP BY o.customer_id ";
            }else{
                $sql .= " GROUP BY ". $group_by;
            }

            $sql .= " ORDER BY o.date_added DESC ";
        
            $qry = $db->query($sql);

            if($qry->num_rows > 0){
                $data = $qry->rows;
            }
        }

        return $data;
    }


    /**
     * @info: Public method to get Order Breakup account details like : 
     *        order_no and breakup details( Like: date, invoice_no, debit_amount, credit_amount) 
     * @param: Basic filter key: 
     *            $db DB Object
     *            $order_ids(Required)      : integer
     *
     * @return : Array resultset
     *
     * @author: Nishu, June 2019
    */   
    public function getOrderLevelBreakupAccountDetails($db, $order_ids) : array{
        $data = array();
        
        if(is_array($order_ids)){
            //Top ensure that invalid order_ids in the array are removed
            $order_ids = array_unique(array_filter(array_map('intval',$order_ids)));
        }
        
        if(!empty($order_ids)){
            
            $order_ids = (is_array($order_ids) ? implode(',', $order_ids) : $order_ids);            

            $sql = "
                    SELECT
                        tbl.*
                    FROM
                        (

                            SELECT 
                                'suborder_invoice' AS type,
                                o.order_id AS order_id,
                                o.order_no,
                                osub.suborder_id AS ref_id,
                                suborder_id AS particular,
                                IF(
                                    (osub.buyer_invoice_id > 0 AND osub.buyer_invoice_id IS NOT NULL), 
                                    CONCAT(invoice_prefix, invoice_no), 
                                    'Tentative Purchase'
                                ) AS doc_ref,
                                DATE(invoice_date) AS date,
                                osub.total AS debit_amount,
                                '' AS credit_amount
                            FROM
                                oc_suborder AS osub
                            INNER JOIN
                                oc_order AS o ON o.order_id = osub.order_id
                            WHERE
                                osub.order_id IN (". $order_ids .") 
                                    AND o.date_added >= '".$this->_date_stmt_available." 00:00:00'
                                    AND osub.order_status_id > 0
                                    AND osub.order_status_id != 2
                                     
                        UNION

                            SELECT 
                                'order_payment' AS type,
                                o.order_id,
                                o.order_no,
                                op.payment_id AS ref_id,
                                op.payment_gateway AS particular,
                                op.merchant_txn_id AS doc_ref,
                                IF(op.txn_date_time IS NULL
                                        OR DATE(op.txn_date_time) = '0000-00-00',
                                    DATE(op.date_added),
                                    DATE(op.txn_date_time)) AS date,
                                IF(op.amount < 0, ABS(op.amount), 0) AS debit_amount,
                                IF(op.amount > 0, op.amount, 0) AS credit_amount
                            FROM
                                oc_order_payment AS op
                            INNER JOIN
                                oc_order AS o ON o.order_id = op.order_id
                            WHERE
                                o.order_id IN (". $order_ids .") 
                                    AND o.date_added >= '".$this->_date_stmt_available." 00:00:00'
                                    AND op.successfull = 1
                                    AND op.payment_gateway != 'wsb_credit'

                        UNION

                            SELECT 
                                'credit_note' AS type,
                                o.order_id,
                                o.order_no,
                                cn.credit_note_id AS ref_id,
                                CONCAT('Credit Note Ref: ', cn.suborder_id) AS particular,
                                CONCAT(cn.credit_note_prefix, cn.credit_note_no) AS doc_ref,
                                DATE(cn.date_added) AS date,
                                (
                                cn.cod_failed_penalty + 
                                cn.less_cash_discount + 
                                IF(cn.other_charges < 0, ABS(cn.other_charges), 0)
                                ) AS debit_amount,
                                (
                                cn.credit_note_amount +
                                IF(cn.other_charges > 0, cn.other_charges, 0)
                                )
                                AS credit_amount
                            FROM
                                oc_credit_note AS cn
                            INNER JOIN
                                oc_order AS o On o.order_id = cn.order_id
                            WHERE
                                o.order_id IN (". $order_ids .")
                                    AND cn.credit_note_status = 1
                                    AND o.date_added >= '".$this->_date_stmt_available." 00:00:00'

                        ) tbl
                    
                    ORDER BY
                        -tbl.date DESC
                    ";

            $qry = $db->query($sql);

            if($qry->num_rows > 0){
                $data = $qry->rows;
            }
        }

        return $data;
    }

    /**
     * @info: Private method to get prepare SQL sub query dynamically for oc_suborder table
     * @param: $filter_data array- --->  array(where = "", having = "")
     * @return: String
     *
     * @author: Nishu, 4th July 2019
    */
    private function getPrepareOcSuborderSqlQuery($filter_data) : string{
        $sql = " 
                INNER JOIN
                    (
                        SELECT 
                            order_id
               ";
        
        if(!empty($filter_data['select'])){
            $sql .= ", " . implode(', ', $filter_data['select']);
        }

        $sql .= "
                        FROM  
                            oc_suborder 
                        WHERE 
                            order_status_id > 0 ";

        if(!empty($filter_data['where'])){
            $sql .= " AND " . implode(' AND ', $filter_data['where']);
        }
        $sql .= "
                        GROUP BY 
                            order_id 
                ";
        if(!empty($filter_data['having'])){
            $sql .= " ". $filter_data['having'];
        }

        // HAVING 
        //   ( 
        //     SUM(IF(order_status_id=2, 1, 0)) = COUNT(DISTINCT suborder_id) 
        //     OR DATEDIFF(NOW(), delivered_date) >= 30
        //   )  
        $sql .= " ) AS osub ON osub.order_id = o.order_id ";

        return $sql;
    }

    /**
     * @info: Private method to get prepare SQL sub query dynamically for oc_order_payment table
     * @param: $filter_data array- --->  array(where = "", having = "")
     * @return: String
     *
     * @author: Nishu, 4th July 2019
    */
    private function getPrepareOcOrderPaymentSqlQuery($filter_data) : string{
        $sql = " 
                LEFT JOIN
                    (
                        SELECT 
                            order_id,
                            COALESCE(SUM(IF(amount > 0
                                            AND (payment_gateway = 'cashback'
                                                 OR payment_gateway = 'coupon'),
                                                amount,
                                                0)),
                                            0) AS cashback_coupon,

                            COALESCE(SUM(IF(amount > 0 
                                        AND payment_gateway != 'cashback'
                                        AND payment_gateway != 'coupon'
                                        AND txn_status != 'cheque_deposited',
                                    amount,
                                    0)),
                                0) AS payment_received,

                            COALESCE(SUM(IF(amount < 0
                                            AND payment_gateway != 'cashback'
                                            AND payment_gateway != 'coupon',
                                        amount,
                                        0)),
                                    0) AS refund  ";
        
        if(!empty($filter_data['select'])){
            $sql .= ", " . implode(', ', $filter_data['select']);
        }

        $sql .= "

                        FROM
                            oc_order_payment
                        WHERE
                            successfull = 1
                                AND payment_gateway != 'wsb_credit' ";

            if(!empty($filter_data['where'])){
                $sql .= " AND " . implode(' AND ', $filter_data['where']);
            }
            $sql .= "
                        GROUP BY
                            order_id ";
                if(!empty($filter_data['having'])){
                    $sql .= " ". $filter_data['having'];
                }

            $sql .="
                    ) AS op ON op.order_id = o.order_id ";

        return $sql;
    }

    /**
     * @info: Private method to get prepare SQL sub query dynamically for oc_credit_note table
     * @param: $filter_data array- --->  array(where = "", having = "")
     * @return: String
     *
     * @author: Nishu, 4th July 2019
    */
    private function getPrepareOcCreditNoteSqlQuery($filter_data) : string{
        $sql = " 
                LEFT JOIN
                    (
                        SELECT 
                            COALESCE(SUM(credit_note_amount), 0) AS cn_amount,
                            COALESCE(SUM(cod_failed_penalty), 0) AS cod_failed_penalty,
                            COALESCE(SUM(less_cash_discount), 0) AS less_cash_discount,
                            COALESCE(SUM(other_charges), 0)      AS other_charges, 
                            order_id  ";
        
        if(!empty($filter_data['select'])){
            $sql .= ", " . implode(', ', $filter_data['select']);
        }

        $sql .= "
                        FROM
                            oc_credit_note
                        WHERE
                            credit_note_status = 1 ";

            if(!empty($filter_data['where'])){
                $sql .= " AND " . implode(' AND ', $filter_data['where']);
            }
            $sql .= "
                        GROUP BY 
                            order_id ";

            if(!empty($filter_data['having'])){
                $sql .= " ". $filter_data['having'];
            }
            $sql .= "
                    ) AS cn ON cn.order_id = o.order_id ";

        return $sql;
    }

    /**
     * @info: Private method to get SQL query string for given filters or clauses 
     *        
     * @param: array $data
     *           keys:- select,
     *                  suborder      => array("select"=>array(),"where" = array(), "having" = ""),
     *                  order_payment => array("select"=>array(),"where" = array(), "having" = ""),
     *                  credit_note   => array("select"=>array(),"where" = array(), "having" = ""),
     *                  where
     *                  sort,
     *                  having
     * @note: table alias name for select list or outter where clause
     *         oc_suborder      => osub,
     *         oc_order_payment => op,
     *         oc_credit_note   => cn
     *
     *  ( If mentioning any field in select list using alias, Kindly make sure these fileds coming in subquery! )
     *
     * @author: Nishu, July 2019
    */
    private function getSqlQueryForOrderBalance($data) : string{
        $sql = "
                SELECT 
                    ROUND((
                        - COALESCE(o.total, 0) 
                        + COALESCE(cn.cn_amount, 0) 
                        - COALESCE(cn.cod_failed_penalty, 0) 
                        + COALESCE(op.cashback_coupon, 0)
                        + COALESCE(op.payment_received, 0)
                        + COALESCE(op.refund, 0)
                        - COALESCE(cn.less_cash_discount, 0)
                        + COALESCE(cn.other_charges, 0)
                        ),
                    2 ) AS order_bal,
                    o.order_id ";
        
        if(!empty($data['select'])){
            $sql .= ", " . implode(', ', $data['select']);
        }

        $sql .= "
                FROM
                    oc_order AS o ";

        //Subquery Join query for oc_suborder
        $suborder_filter = $data['suborder'] ?? array();
        $sql .= self::getPrepareOcSuborderSqlQuery($suborder_filter);

        //Subquery Join query for oc_order_payment
        $order_payment_filter = $data['order_payment'] ?? array();
        $sql .= self::getPrepareOcOrderPaymentSqlQuery($order_payment_filter);

        //Subquery Join query for oc_credit_note
        $credit_note_filter = $data['credit_note'] ?? array();
        $sql .= self::getPrepareOcCreditNoteSqlQuery($credit_note_filter);

        $sql .= "
                WHERE
                    o.currency_code = 'INR'
                        AND o.store_id IN (". WSB_STORES_ID .")
                        AND o.stock_transfer = 0
                        AND o.franchise_id = 0  ";
            if(!empty($data['where'])){
                $sql .= " AND ". implode(" AND ", $data['where']);
            }

            $sql .= " GROUP BY o.order_id ";
        if(!empty($data['having'])){
            $sql .= $data['having'];
        }
        
        return $sql;
    }

    /**
     * Public method to get order wise balance with filters 
     * @param : $data
     *              Keys --> 
     * @return: string
     * @author: Nishu, July 2019
    */
    public static function getOrderBalance($db, $query_data): array{
        $order_wise_balance = array();
        $sql = (new self)->getSqlQueryForOrderBalance($query_data);

        if(!empty($sql)){

            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                $result = $qry->rows;
                $order_wise_balance = array_combine(
                                        array_column($result, 'order_id'), 
                                        $result);
            }
        }

        return $order_wise_balance;
    }

}// close OrderAccounts class
?>
