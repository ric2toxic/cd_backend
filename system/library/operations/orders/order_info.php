<?php

/**
 * Main Class for getting all Order Related Info.
 * Majority of functions here will be static.
 * Major usage: getOrderInfo method
 * It is recommended to utilize the getOrderInfo method to create
 * specfic methods in this class fetching a particular data.
 * Specific selectors can be made and stored in this class as well.
 * @Author Madhur, 2017
 */
class OrderInfo {

    /**
     * Default Selector
     * Using this we can specify tablewise fields to select, and sorting rules to apply.
     * array - ( [table_name without DB_PREFIX] => array ( 'select' => array (<fields>)  OR <field>,
     *                                                     'sort' => array(<field1> => <ASC or DESC>,
     *                                                                     <field2> => <ASC or DESC>, .. and so on
     *                                                                    )
     *                                                   )
     *         )
     * @note: This is a default selector. It does not include all order tables,
     * but some of the commonly used ones. If getOrder is not specified selector, then this is used.
     * @note: If there is no 'select' key in the table specifc array, or 'select' key points to empty array,
     * then it selects all (*) the fields from table.
     * @note: 'select' key can have a single field OR array of fields.
     * @note: 'sort' key can be used to define multi-level sorts as well.
     * @author Madhur
     */
    private static $_default_selector = array('order' => array(),
        'order_history' => array('sort' => array('suborder_id' => 'ASC',
                'date_added' => 'ASC'
            )
        ),
        'order_option' => array('sort' => array('order_product_id' => 'ASC')),
        'order_product' => array('sort' => array('order_product_id' => 'ASC')),
        'order_total' => array('sort' => array('suborder_id' => 'ASC',
                'sort_order' => 'ASC'
            )
        ),
        'suborder' => array('sort' => array('suborder_id' => 'ASC'))
    );

    /**
     * Suborder Id existence information in various Order tables
     * Private static - used by getOrder
     * array - ( [table_name without DB_PREFIX] => <Boolean whether suborder_id exists> )
     */
    private static $_suborderid_exists = array('order' => false,
        'order_custom_field' => false,
        'order_history' => true,
        'order_option' => true,
        'order_payment' => false,
        'order_product' => true,
        'order_total' => true,
        'seller_debit_note' => true,
        'seller_invoice' => true,
        'suborder' => true
    );

    /**
     * General method to get Order details given order_id and/or suborder_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (required) int Or array
     *                   If array is passing Suborder_id must empty
     * $suborder_id - Suborder ID (string, not mandatory,
     *                            defaults to '' meaning all suborders returned for given $order_id)
     * $selector - Selector array for fields to select from specific order tables with sorting rules etc.
     *              Defaults to '', meaning $_default_selector of this table is used.
     *
     * @return:
     * If passing single OrderId:
     *         array('order' => array(<field> => <value>,
     *                        <tables like order_payment> => array of rows for order_id)
     *                       ),
     *               'suborder' => array(
     *                        <suborder_id 1> => array(<field> => <value>,
     *                                                 <tables like order_product> => array of rows for suborder_id
     *                                                )
     *                          )
     *      )
     * If passing Array of OrderIds:
     *         array(
     *               <order-id> => array(
     *                             'order' => array(<field> => <value>,
     *                                          <tables like order_payment> => array of rows for order_id)
     *                       ),
     *                             'suborder' => array(
     *                                              <suborder_id 1> => array(<field> => <value>,
     *                                                 <tables like order_product> => array of rows for suborder_id
     *                                                )
     *                             )
     *                          )
     *      )
     * @author Madhur
     */
    public static function getOrderInfo($db, $order_id, $suborder_id = '', $selector = '') {

        // Using default selector, if no selector provided
        if (empty($selector))
            $selector = self::$_default_selector;

        // Initializing return variable
        $order_info = array();
        if (!is_array($order_id)) {
            $order_info['order'] = array();
            $order_info['suborder'] = array();
        }

        // Looping over the selector tables
        foreach ($selector as $table => $rules) {

            $sql = "SELECT ";

            // Checking if 'select' rules are defined
            if (!empty($rules['select'])) {

                // We need to ensure that order_id and suborder_id are always fetched
                $sql .= "order_id";
                if (!empty(self::$_suborderid_exists[$table])) {
                    $sql .= ", suborder_id";
                }

                if (is_array($rules['select'])) {
                    // order_id and suborder_id should not be queried twice in the same query for optimization purposes
                    if (array_search('order_id', $rules['select']) !== false) {
                        unset($rules['select'][array_search('order_id', $rules['select'])]);
                    }
                    if (array_search('suborder_id', $rules['select']) !== false) {
                        unset($rules['select'][array_search('suborder_id', $rules['select'])]);
                    }

                    if (!empty($rules['select'])) { // Checking if it still has fields after unset operations
                        $sql .= ", " . implode(", ", $rules['select']);
                    }
                } else { // Only single select field specified
                    // Need to ensure that it is neither order_id OR suborder_id to avoid querying same data
                    if ($rules['select'] != 'order_id' && $rules['select'] != 'suborder_id') {
                        $sql .= ", " . $rules['select'];
                    }
                }
            } else { // all fields to be obtained
                $sql .= "*";
            }

            $sql .= " FROM " . DB_PREFIX . $table;
            if(is_array($order_id)){
                $sql .=  " WHERE order_id IN (" . implode(',', $order_id) . ") ";
            }else{
                $sql .=  " WHERE order_id = '" . (int) $order_id . "' ";
            }

            // Use suborder_id clause if provided, and suborder_id field exists in this table
            if ($suborder_id && !empty(self::$_suborderid_exists[$table])) {
                $sql .= " AND suborder_id LIKE '" . $db->escape($suborder_id) . "'";
            }

            // Adding Sort Rules
            if (!empty($rules['sort'])) {
                $sql .= " ORDER BY ";
                $sql .= implode(", ", array_map(
                                function($key, $value) {
                            return $key . " " . $value;
                        }, array_keys($rules['sort']), $rules['sort']
                        )
                );
            }

            // Executing SQL query
            $query = $db->query($sql);

            if ($query->num_rows) {

                if(is_array($order_id)){//Whenever array of order_ids passed
                    $order_info = self::setMultipleOrderDetails($query, $table, $order_info);
                }else{
                    $order_info = self::setSingleOrderDetails($query, $table, $order_info);
                }
                
            } // close if ($query->num_rows)
        } // close for loop over tables

        return $order_info;
    }

    /**
     * Private Method to set Order details
     *    When multiple order_ids are passed (New Format Added)
     * @param: $query- Query result
     * @author: Nishu, 31st July 2018
    */
    private static function setMultipleOrderDetails($query, $table, $order_info){
       
        foreach ($query->rows as $row) {
            $oid = $row['order_id'];
            
            // If table does not have suborder_id, then results will go to 'order' key
            if (empty(self::$_suborderid_exists[$table])) {

                // If table is oc_order
                if ($table == 'order') { // We should receive only one row
                    foreach ($row as $field => $value) {
                        $order_info[$oid]['order'][$field] = $value;
                    }
                } else { // table name will be key inside 'order'
                    $order_info[$oid]['order'][$table][] = $row;
                }
        
            } else { // Table has suborder_id

                $sid = $row['suborder_id'];
            
                // If table is 'oc_suborder'
                if ($table == 'suborder') {
                    foreach ($row as $field => $value) {
                        $order_info[$oid]['suborder'][$sid][$field] = $value;
                    }
                } else {
                    $order_info[$oid]['suborder'][$sid][$table][] = $row;
                }
            }
        }

        return $order_info;
    }

    /**
     * Private Method to set Order details
     *    When Single order_id is passed (Existing Format) 
     * @param: $query- Query result
     * @author: Nishu, 31st July 2018
    */
    private static function setSingleOrderDetails($query, $table, $order_info){

        // If table does not have suborder_id, then results will go to 'order' key in return $order_info
        if (empty(self::$_suborderid_exists[$table])) {

            // If table is oc_order
            if ($table == 'order') { // We should receive only one row
                foreach ($query->row as $field => $value) {
                    $order_info['order'][$field] = $value;
                }
            } else { // table name will be key inside 'order'
                $order_info['order'][$table] = $query->rows;
            }
        } else { // Table has suborder_id
            foreach ($query->rows as $row) {
                // Get suborder_id from row and check if the suborder_id key exists
                if (!isset($order_info['suborder'][$row['suborder_id']])) {
                    $order_info['suborder'][$row['suborder_id']] = array();
                }

                // If table is 'oc_suborder'
                if ($table == 'suborder') {
                    foreach ($row as $field => $value) {
                        $order_info['suborder'][$row['suborder_id']][$field] = $value;
                    }
                } else {
                    if (!isset($order_info['suborder'][$row['suborder_id']][$table])) {
                        $order_info['suborder'][$row['suborder_id']][$table] = array();
                    }
                    $order_info['suborder'][$row['suborder_id']][$table][] = $row;
                }
            }
        }

        return $order_info;
    }

// close getOrderInfo function

    /**
     * Method to get order_no given order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: order_no (string) if exists, else false
     * @author Madhur
     */
    public static function getOrderNo($db, $order_id) {
        $selector = array('order' => array('select' => 'order_no'));
        $order_info = self::getOrderInfo($db, $order_id, '', $selector);
        if (!empty($order_info['order']['order_no'])) {
            return $order_info['order']['order_no'];
        } else {
            return false;
        }
    }

// close getOrderNo function

    /**
     * Method to get customer_id given order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: customer_id (int) if exists, else 0
     * @author Madhur
     */
    public static function getCustomerIdFromOrder($db, $order_id) {
        $selector = array('order' => array('select' => 'customer_id'));
        $order_info = self::getOrderInfo($db, $order_id, '', $selector);
        if (!empty($order_info['order']['customer_id'])) {
            return (int) $order_info['order']['customer_id'];
        } else {
            return 0;
        }
    }

// close getCustomerIdFromOrder function
    
    /**
     * Method to get customer_id given order_no
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_no - Order No. (varchar, required)
     * @return: customer_id (int) if exists, else 0
     * @author Nilesh, 2018
     */
    public static function getCustomerIdFromOrderNo($db, $order_no) {
        $sql = "SELECT customer_id 
                FROM " . DB_PREFIX . "order 
                WHERE order_no = '" . $db->escape($order_no) . "'
                LIMIT 1";
        $customer_id_query = $db->query($sql);
        if ($customer_id_query->num_rows) {
            return (int) $customer_id_query->row['customer_id'];
        } else {
            return 0;
        }
    }

// close getCustomerIdFromOrderNo function
    
    /**
     * Method to checkOrderNoExistsOrNot for given order_no
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_no - Order No. (varchar, required)
     * @return: true if exists, else false
     * @author Nilesh, 2018
     */
    public static function checkOrderNoExistsOrNot($db, $order_no) {
        $sql = "SELECT order_id 
                FROM " . DB_PREFIX . "order 
                WHERE order_no = '" . $db->escape($order_no) . "'
                LIMIT 1";
        $query = $db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

// close checkOrderNoIsExistsOrNot function

    /**
     * Method to get customer telephone from a given order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: telephone if exists, else false
     * @author Madhur
     */
    public static function getCustomerTelephoneFromOrder($db, $order_id) {
        $selector = array('order' => array('select' => 'telephone'));
        $order_info = self::getOrderInfo($db, $order_id, '', $selector);
        if (!empty($order_info['order']['telephone'])) {
            return $order_info['order']['telephone'];
        } else {
            return false;
        }
    }

// close getCustomerTelephoneFromOrder function

    /**
     * Method to get total advance for a particular order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: Total advance value (float)
     * @warning: This method will work only for order code_version >= 2.0
     * @author Madhur
     */
    public static function getTotalAdvance($db, $order_id, $include_wsb_credit = true) {

        // Get total advance for the given Order ID
        $sql = "SELECT SUM(amount) as advance
                FROM " . DB_PREFIX . "order_payment
                WHERE order_id = " . (int) $order_id . "
                  AND successfull = 1
                  AND amount > 0 ";
                  
        if (!$include_wsb_credit) {
            $sql .= " AND payment_gateway <> 'wsb_credit' ";
        }
        
        $result = $db->query($sql);

        $total_advance = 0;
        if ($result->num_rows) {
            $total_advance = (float) ($result->row['advance']);
        }

        return $total_advance;
    }

// close getTotalAdvance function

    public static function getTotalCredit($db, $order_id) {
        $credit = 0;
        // Getting overall credit note value used in this order
        $sql = "SELECT sum( amount ) as amount ";
        $sql .= "FROM " . DB_PREFIX . "customer_transaction ";
        $sql .= "WHERE order_id = '" . (int) $order_id . "' AND ";
        $sql .= "amount < 0 ";
        $result = $db->query($sql);
        if ($result->num_rows && (float) ($result->row['amount']) < 0) {
            $credit = (float) ($result->row['amount']);
        }
        return $credit;
    }

    /**
     * Method to get advance breakup suborderwise for a particular order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * $suborder_id - Suborder ID (string, not mandatory, defaulted to '')
     * @return: array(<suborder_id> => array('advance' => <value>,
     *                                       'invoice_no' => <invoice_no>)
     *          if suborder_id given, then only the corresponding suborder_id is returned in the array
     * @note: invoice_no > 0 implies that advance value for the particular suborder should be considered freezed.
     * @warning: This method will work only for order code_version >= 2.0
     * @author Madhur
     */
    public static function getAdvanceBreakup($db, $payment_id) {

        $advance_breakup = array();

//        $order_info = self::getOrderInfo($db,
//                                         $order_id,
//                                         $suborder_id,
//                                         array('suborder'=>array('select'=> array('invoice_no','custom_totals'))));

        $order_info = AdvanceVoucher::getAdvanceVouchersByData($db, array(
                    'payment_id' => $payment_id
                        )
        );

        // Looping over suborders
        if (!empty($order_info)) {
            foreach ($order_info as $values) {
                $suborder_advance = 0;
                $suborder_id = $values['suborder_id'];
                //$suborder_advance['value'] = (float) $suborder_advance['value'];

                $advance_breakup[$suborder_id] = array(
                    'value' => (float) $values['value'],
                    'user' => $values['user_id'],
                    'locked' => false,
                    'order_id' => $values['order_id'],
                    'suborder_id' => $values['suborder_id'],
                    'advance_voucher_id' => $values['advance_voucher_id']
                );
                $suborder_info = self::getOrderInfo($db, $values['order_id'], $suborder_id, array('suborder' => array(
                                'select' => array('invoice_no')
                            )
                        ))['suborder'][$suborder_id];
                $advance_breakup[$suborder_id]['invoice_no'] = $suborder_info['invoice_no'];
            }
        }
        return $advance_breakup;
    }

// close getAdvanceBreakup function

    /**
     * Method to get store_id from a given order_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: store_id (int) if exists, else -1
     * @author Madhur
     */
    public static function getStoreIdFromOrder($db, $order_id) {
        $selector = array('order' => array('select' => 'store_id'));
        $order_info = self::getOrderInfo($db, $order_id, '', $selector);
        if (isset($order_info['order']['store_id'])) {
            return (int) $order_info['order']['store_id'];
        } else {
            return -1;
        }
    }

// close getStoreIdFromOrder function

    /**
     * Method to get last suborder history
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * $suborder_id - Suborder ID (varchar, required)
     * @return: suborder_id history arr if exists, else blank arr
     * @author Nilesh
     */
    public static function getLastSuborderHistoryInfo($db, $order_id, $suborder_id) {
        // Getting last history for this suborder
        $result = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "order_history
                WHERE order_id = '" . (int) $order_id . "'
                  AND suborder_id = '" . $db->escape($suborder_id) . "'
                ORDER BY date_added DESC LIMIT 1";
        $query = $db->query($sql);
        if ($query->num_rows) {
            $result = $query->row;
        }
        return $result;
    }
    
    /**
     * Method to get pickup city codes list
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_product_ids - Order Product IDs (Array, required)
     * $groupby_key - Group By Column (varchar, optional)
     * @return: pickup city codes list, else blank arr
     * @author MSA
     */
    public static function getPickupCityCodes($db, $order_product_ids, $groupby_key='pickup_city_code')
    {
        $pickup_city_codes = array();
        if(!empty($order_product_ids)) {
            $sql = "SELECT DISTINCT pickup_city_code
                    FROM " . DB_PREFIX . "order_product oop
                    INNER JOIN " . DB_PREFIX . "seller_invoice osi ON (osi.seller_invoice_id = oop.seller_invoice_id)
                    INNER JOIN " . DB_PREFIX . "vat_input_rules ovir ON (ovir.rule_id=osi.vat_input_rule_id)
                    WHERE
                        oop.order_product_id IN (".implode(',', $order_product_ids).")
                    ";
            $result = $db->query($sql);
            if($result->num_rows > 0){
                 $pickup_city_codes = $result->rows;             
            }
            return $pickup_city_codes;        
        }
    }
    /**
     * Static Method to get warehouse details by city code
     * @param:  Database object $db
     * @param:  string $city_code
     * @return: array
     * @author: MSA
     */
    public static function getWarehouseDetailsByCityCode($db, $city_code)
    {
        $data = array();
        if(empty($city_code)){
            return array();
        }
        $pickup_address = array();
        $sql = "SELECT 
                    warehouse_name,
                    address_1,
                    address_2,
                    city,postcode,
                    city_code
                FROM 
                    " . DB_PREFIX . "warehouse_address 
                WHERE 
                    city_code IN('". $db->escape($city_code) ."', 'JP')
                    AND 
                    status = 1
                ORDER BY 
                    NULL
                ";
        $result =  $db->query($sql);
        if($result->num_rows){
           $data =  array_combine(
                                array_column($result->rows, 'city_code'),
                                $result->rows
                            );
           $pickup_address = $data[$city_code] ?? $data['JP'];
        }

        return $pickup_address;    
    }

    /**
     * Public method to get OrderProduct details by op_id
     *@param: $db, $op_id
     *@return Array
     *@Author: Nishu, May 2018
    */
    public static function getOrderProductDetailsByOpId($db, $op_id, $field_list = array()){
        $data = array();
        if(empty($op_id)){
            return $data;
        }

        $select_fields = " ";

        if(!empty($field_list)){
            foreach ($field_list as $value) {
                $select_fields .= " oc_order_product.".$value.",";
            }
        }else{
            $select_fields .= " oc_order_product.* ";
        }
        $select_fields = trim($select_fields, ',');
        $sql = "
                SELECT ".$select_fields."
                  FROM oc_order_product
                WHERE 
                  order_product_id = ".(int)$op_id."
               ";

        $result = $db->query($sql);
        if($result->num_rows > 0){
            $data = $result->row;
        }
        return $data;
    }

    // close getLastSuborderHistoryInfo function

    /*
     * getSuborderInvoices used for get suborder invoices status, invoice amount,  order_status_id ect.
     * @Params: 
     * $db - db object,  
     * $order_id - order_id (int)
     * $suborder_id which will be in array
     * @return: array suborder wise
     * @author: NILESH, 2018
     */

    public static function getSuborderInvoices($db, $order_id, $suborder_id = array()) {

        $result_arr = array();
        $selector = array(
            'order' => array('select' => array(
                    'payment_code'
                )
            ),
            'suborder' => array('select' => array(
                    'order_id',
                    'suborder_id',
                    'buyer_invoice_id',
                    'invoice_no',
                    'order_status_id'
                )
            )
        );
        $order_info = self::getOrderInfo($db, $order_id, '', $selector);

        if (!empty($order_info['suborder'])) {
            foreach ($order_info['suborder'] as $suborder_id_key => $suborder_info) {
                if (!empty($suborder_id)) {
                    if (!(in_array($suborder_id_key, $suborder_id))) {
                        continue;
                    }
                }
                $invoice_status = '';
                if ((int) $suborder_info['order_status_id'] > 0 &&
                        (int) $suborder_info['order_status_id'] != 2 &&
                        (int) $suborder_info['invoice_no'] > 0 &&
                        (int) $suborder_info['buyer_invoice_id'] > 0) {
                    $invoice_status = 'invoiced';
                } elseif ((int) $suborder_info['order_status_id'] > 0 &&
                        (int) $suborder_info['order_status_id'] != 2 &&
                        (int) $suborder_info['invoice_no'] == 0 &&
                        (int) $suborder_info['buyer_invoice_id'] == 0) {
                    $invoice_status = 'uninvoiced';
                } elseif ((int) $suborder_info['order_status_id'] == 2) {
                    $invoice_status = 'cancelled';
                } else {
                    $invoice_status = 'undefined';
                }
                $edit_type_flag = ($invoice_status == 'invoiced') ? 1 : 0;
                $suborder_total_invoice_amount = (float) AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($db, $suborder_id_key, $edit_type_flag);
                $result_arr[$suborder_id_key] = array(
                    'order_id' => $suborder_info['order_id'],
                    'suborder_id' => $suborder_info['suborder_id'],
                    'buyer_invoice_id' => $suborder_info['buyer_invoice_id'],
                    'invoice_no' => $suborder_info['invoice_no'],
                    'order_status_id' => $suborder_info['order_status_id'],
                    'total' => $suborder_total_invoice_amount,
                    'invoice_status' => $invoice_status,
                    'payment_code' => $order_info['order']['payment_code']
                );
            }
        }
        return $result_arr;
    }

// close getSuborderInvoices function

    /*
     * getUninvoicedSuborderAmounts used for get suborder uninvoice amounts of a order.
     * @Params: 
     * $suborder_invoices_array - calling this getSuborderInvoices function
     * @return: (float) total uninvoiced suborder amount default 0
     * @author: NILESH, 2018
     */

    public static function getUninvoicedSuborderAmounts($db, $order_id, $suborder_id = array(), $suborder_invoices_array = array()) {
//        5. getUninvoicedSuborderAmounts($suborder_invoices_array) 
//   - sum(invoice_amt) where invoice_status = 'uninvoiced'
        $total_suborder_uninv_amout = 0;
        if (empty($suborder_invoices_array)) {
            $suborder_invoices_array = self::getSuborderInvoices($db, $order_id, $suborder_id);
        }

        foreach ($suborder_invoices_array as $suborder_id_key => $suborder_info) {
            if ($suborder_info['invoice_status'] == 'uninvoiced') {
                $total_suborder_uninv_amout += $suborder_info['total'];
            }
        }

        return (float) $total_suborder_uninv_amout;
    }

    /**
     * @info: Public static method to get all SellerInvoices by OrderId
     * @param: $db DB Object, $order_id Integer
     * @return: Array
     * @author: Nishu, June 2018
    */
    public static function setAllSellerInvoicesByOrderId($db, $order_id){
        $data = array();
        if(!empty($order_id)){
            $sql = "
                    SELECT 
                        * 
                    FROM
                        " . DB_PREFIX . "seller_invoice 
                    WHERE
                        order_id = ". (int)$order_id;
            
            //Executing DataBase Query
            $result = $db->query($sql);

            //Check If any SellerInvoice is exist for this $order_id
            if($result->num_rows > 0){
                //Assign all result rows
                $data = $result->rows; 

                //Make SellerInvoiceId as Array Key
                $data = array_combine(
                            array_column($data, 'seller_invoice_id'), 
                            $data
                        );
            }

        }
        return $data; //Return Result Set or empty Array
    }

    /**
    * To get the dispatch date for the order by suborder_id 
    * having order status id's(4, 5, 13, 14, 15)
    * 
    * @param $db          db          object
    * @param $suborder_id suborder_id array
    * @author Ashish, July 2018 
    * @return array 
    */
    public static function getDispatchDate($db, $suborder_id)
    {
        if (!empty($suborder_id)) {
            $sql = "SELECT 
                        suborder_id, 
                        MIN(ooh.date_added) AS dispatch_date 
                    FROM 
                        " . DB_PREFIX . "order_history ooh 
                    WHERE 
                        suborder_id IN ('". implode("','", $suborder_id) ."') 
                        AND ooh.order_status_id IN ('4', '5', '13', '14', '15') 
                    Group BY suborder_id";

            $query = $db->query($sql);
            
            $result = array();
            if ($query->num_rows > 0) {
                foreach ($query->rows as $record) {
                    $result[$record['suborder_id']] = $record['dispatch_date'];
                }
            }
            return $result;
        }
    }

    /**
     * Method for order cancelled
     * @param: $order_id, $suborder_id
     * @return : true or false
     * nilesh, 2017
     * update by vikas , 2018
     */
    public static function checkForOrderCancelationIsApplicable($db, $order_id, $suborder_id) {

      $sql = "
                SELECT 
                    oop.order_product_id
                
                FROM 
                    " . DB_PREFIX ."order_product oop
                
                LEFT JOIN 
                    " . DB_PREFIX ."return ortn ON ortn.order_product_id = oop.order_product_id AND ortn.active_row = 1
              
                WHERE 
                        oop.order_id = '" . (int) $order_id . "' 
                        AND 
                        oop.suborder_id = '" . $db->escape($suborder_id) . "'
                        AND 
                        oop.seller_invoice_id > 0 
                        AND 
                            ( ortn.return_action_id NOT IN (".implode(',', CLOSED_ACTION_IDS).") 
                              AND ortn.return_action_id IS NOT NULL 
                            )
              LIMIT 1 ";
      $query = $db->query($sql);
      if( $query->num_rows ){
        return array('status' => 0, 'error' => 'Order cannot be Canceled. Because Return has not completed.');
      } 

      /*Checking suborder for return section generated active status CN*/
      $param = array("credit_note_status" => 1);
      $return_info = new ReturnInfo();
      $return_all_cn = $return_info->getAllCNBySubOrderId($db, $order_id, $suborder_id, $param);
      if(!empty($return_all_cn)) {
          return array('status' => 0, 'error'=> 'This suborder cannot be Cancelled, as there are some Active Credit Notes generated against the same.');
        }
      /*Checking suborder for return section generated active status CN*/ 

    }
    
    /**************************************************
    @description: static function to get customers' orders with all suborders either uninvoiced or cancelled 
    @params: $db: db instance
             $customer_ids: (array)
    @return: (string) comma separated order_ids
    @author: Anurag Jain (Aug 2018)
    **************************************************/
    public static function getOrdersWithAllUninvoicedOrCancelledSuborders($db, $customer_ids) {
        $ids = implode(",", $customer_ids);
        $sql = "SELECT 
                    GROUP_CONCAT(DISTINCT o.order_id) as order_id, 
                    count(o.order_id) AS total_suborders,
                    IF((osub.order_status_id = '2' 
                        OR (osub.invoice_no = '0' 
                            AND (osub.buyer_invoice_id = '0' OR osub.buyer_invoice_id IS NULL))), 'true', 'false') as eligibility ,
                    SUM(IF((osub.order_status_id = '2' 
                        OR (osub.invoice_no = '0' 
                            AND (osub.buyer_invoice_id = '0' OR osub.buyer_invoice_id IS NULL))), 1, 0)) as sum_eligible     
                FROM 
                    ". DB_PREFIX ."order o
                    INNER JOIN ". DB_PREFIX ."suborder osub
                        ON (osub.order_id = o.order_id)
                WHERE o.customer_id IN (". $ids .")
                    AND osub.order_status_id > '0'
                    AND o.store_id IN (" . WSB_STORES_ID . ")
                GROUP BY o.order_id
                HAVING eligibility = 'true'
                    AND total_suborders = sum_eligible";
        $result = $db->query($sql);
        if($result->num_rows) {
            $order_ids_array = array_column($result->rows, 'order_id');
            if(!empty($order_ids_array)) {
                return $order_ids_array;
            }
        }
        return false;
    }


    /**
     * Public static function to get order ids by given order nos
     * @author: Nishu, Jan 2019
    */
    public static function getOrderIdsByOrderNos($db, $order_nos ): array {
        $data = array();
        if(!empty($order_nos)){
            if(is_array($order_nos)){
                $order_nos = implode(',', $order_nos);
            }

            $sql = "SELECT
                        order_id,
                        order_no,
                        total
                    FROM
                        ".DB_PREFIX."order 
                    WHERE
                        order_no IN (". $order_nos .")
                   ";
            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                 $data = array_combine(array_column($qry->rows, 'order_no'), $qry->rows );
            }
        }
        return $data;
    }

    /**
     * Public method to get payment details related to given multiple order ids
     * @param: $db, $order_ids
     * @return: array
     * @author: Nishu, Jan 2019
    */
    public static function getPaymentDetailsByOrderIds($db, array $order_ids, $consider_wsb_credit = 1):array {
        $result = array();
        $whr = "";
        if($consider_wsb_credit == 0){
            $whr = " AND payment_gateway != 'wsb_credit' ";
        }
        $sql = "
                SELECT
                    payment_id,
                    txn_date_time as payment_date,
                    payment_gateway,
                    payment_mode,
                    merchant_txn_id,
                    rec_pay_id,
                    order_id,
                    txn_status,
                    txn_date_time,
                    amount
                FROM
                    ".DB_PREFIX."order_payment 
                WHERE
                    order_id IN (".implode(',', $order_ids).")
                    AND successfull = 1 ". $whr ."
                GROUP BY
                    payment_id
               ";
        $qry = $db->query($sql);
        if($qry->num_rows > 0){
            foreach ($qry->rows as $value) {
                $order_id = $value['order_id'];

                $result['payments'][$order_id][] = $value;

                //Used WSB Credit Balance
                $result['used_wsb_credit'][$order_id] = $result['used_wsb_credit'][$order_id] ?? 0; 
                if( $value['payment_gateway'] == 'wsb_credit' ){
                    $result['used_wsb_credit'][$order_id] += $value['amount'];
                }

                //Cashback coupon applied in given order ids
                $result['cashback_coupon'][$order_id] = $result['cashback_coupon'][$order_id] ?? 0; 
                if(
                    $value['amount'] > 0 &&
                    ($value['payment_gateway'] == 'cashback' || 
                    $value['payment_gateway'] == 'coupon' )
                ){
                    $result['cashback_coupon'][$order_id] += $value['amount'];
                }

                //Paid amount by Customer 
                $result['paid_amt'][$order_id] = $result['paid_amt'][$order_id] ?? 0;
                if( $value['amount']   > 0 
                    && $value['payment_gateway'] != 'wsb_credit' 
                    && $value['payment_gateway'] != 'cashback'
                    && $value['payment_gateway'] != 'coupon' 
                    && $value['txn_status']      != 'cheque_deposited'
                ){
                    $result['paid_amt'][$order_id] += $value['amount'];
                }

                //Actually refunded amount to customer for given order ids
                $result['refund_amt'][$order_id] = $result['refund_amt'][$order_id] ?? 0;
                if( 
                    $value['amount'] < 0 
                    && $value['payment_gateway'] != 'wsb_credit' 
                    && $value['payment_gateway'] != 'cashback'
                    && $value['payment_gateway'] != 'coupon'
                ) {
                    $result['refund_amt'][$order_id] += $value['amount'];
                }
            }
        }

        return $result;
    }

    /**
     * @info: public methos to get non-cancelled order(s) total for given customer_ids array
     * @param: int $customer_id
     * @return: Array
     * @author: Nishu, Jan 2019
    */
    public static function getOrderTotalsByCustomerIds($db, int $customer_id, $order_id = 0 ){
        $data = array();
        $whr  = '';
        if(!empty($order_id)){
            $whr = " AND o.order_id != ". (int)$order_id;
        }
        $sql = "
                SELECT 
                    COALESCE(SUM(so.total), 0) AS order_total,
                    GROUP_CONCAT(DISTINCT o.order_id) AS  order_ids,
                    o.customer_id
                FROM
                    ".DB_PREFIX."order AS o
                INNER JOIN
                    ".DB_PREFIX."suborder AS so ON o.order_id = so.order_id
                WHERE
                    o.customer_id = ". (int)$customer_id . $whr ." 
                    AND so.order_status_id  > 0 
                    AND so.order_status_id != ".ORDER_STATUS['Canceled']."
                    AND o.payment_code = 'wsb_credit'
                GROUP BY
                    o.customer_id
               ";
        $qry = $db->query($sql);
        if($qry->num_rows > 0){
            $data = $qry->row;
        }
        return $data;
        
    }

    /**
     * Public method to get Order balance by given order id
     * @author: Nishu, Feb 2019
    */
    public static function getOrderBalanceAmount($db, int $order_id){

        //No extra filters/checks and filed list required,
        //Only needs Order Balance is required against given order_id, So Pass order_id as filter 
        $data = array(
                    "where"         => array(" o.order_id = ". (int)$order_id),
                    "suborder"      => array("where" => array(" order_id = ". (int)$order_id) ),
                    "order_payment" => array("where" => array(" order_id = ". (int)$order_id) ),
                    "credit_note"   => array("where" => array(" order_id = ". (int)$order_id) )
                );

        //get Order Balance 
        $order_wise_bal = OrderAccounts::getOrderBalance($db, $data);

        $order_bal = $order_wise_bal[$order_id]['order_bal'] ?? 0; 

        $order_bal = round($order_bal, 2);

        return $order_bal;
    }

    /**
     * Public method to get order_no  by given order_id
     * @author: Nishu, Feb 2019
    */
    public static function getOrderNoByOrderId($db, $order_id){
        $sql = "SELECT order_no 
                FROM 
                    ".DB_PREFIX."order
                WHERE
                    order_id = ".(int)$order_id."
               ";
        $q = $db->query($sql);
        $order_no = $q->row['order_no'] ?? NULL;

        return $order_no;
    }

    /**
     * Public method to check is given order_id related order in missing state
     * @param: $db, $order_id
     * @return: int
     * @author: Nishu, Feb 2019
    */
    public static function checkIsOrderMissingStatus($db, $order_id){
        $is_order_missing = 0;
        $sql = "
                SELECT 
                    SUM(order_status_id) AS order_status
                FROM
                    ".DB_PREFIX."suborder 
                WHERE 
                    order_id = ".(int)$order_id."
                GROUP BY
                    order_id
                HAVING 
                    order_status = 0
               ";
        $result = $db->query($sql);
        if($result->num_rows > 0){
            $is_order_missing = 1;
        }
        return $is_order_missing;
    }

    /**
     * Public method to get missing order_ids comma seperated from given order_ids 
     * @param: $db, $order_ids
     * @return: int
     * @author: Nishu, Feb 2019
    */
    public static function getNotMissingOrdersFromGivenOrders($db, $order_ids){
        $not_missing_order_ids = '';
        $sql = "
                SELECT 
                    SUM(order_status_id) AS order_status,
                    order_id
                FROM
                    ".DB_PREFIX."suborder 
                WHERE 
                    order_id IN (".$order_ids.")
                GROUP BY
                    order_id
                HAVING 
                    order_status > 0
               ";
        $result = $db->query($sql);
        if($result->num_rows > 0){
            $not_missing_order_ids = array_column($result->rows, 'order_id');
            $not_missing_order_ids = implode(',', $not_missing_order_ids);
        }
        return $not_missing_order_ids;
    }

    /**
     * Public method to get Order total amount for given order ids 
     * Example: used in to calculate order balance amount
     * @return: $amount
     * @author: Nishu, Feb 2019
    */
    public static function getOrderTotalForGivenOrderIds($db, $order_ids){
        $order_total = 0;

        $sql = "
                SELECT
                    COALESCE(SUM(total), 0) AS order_total
                FROM
                    ".DB_PREFIX."order
                WHERE
                    order_id IN (".$order_ids.")
               ";
        $query = $db->query($sql);
        if($query->num_rows > 0){
            $order_total = $query->row['order_total']; 
        }

        return $order_total;
    } 

    /**
     * Public method to check if all suborder marked as cancelled
     * @param: int $order_id
     * @return: int 0/1
     * @author: MSA, Feb 2019
    */
    public static function isAllSuborderMarkedCancelled($db, int $order_id): int
    {
        $sql = "
                SELECT 
                    count(suborder_id) as suborders_not_cancelled
                FROM
                    ".DB_PREFIX."suborder
                WHERE
                    order_status_id != '".ORDER_STATUS['Canceled']."'
                    AND
                    order_id = '".(int)$order_id."'

                ";
        $result = $db->query($sql);
        $suborders_not_cancelled = 0;
        if($result->num_rows) {
            $suborders_not_cancelled = $result->row['suborders_not_cancelled'];
        }
        if($suborders_not_cancelled) {
            return 0;  // not all suborder marked as cancelled
        }else{
            return 1; // all suborder marked as cancelled
        }
    }

    /**
     * @info: Public static method to get order(s) by customer_ids for given order_statuses
     * @param: $db, array $customer_ids, array $order_statuses
     * @return: array
     * @author: Nishu, April 2019
    */
    public static function getOrdersForGivenStatusByCustomerIds($db, $customer_ids, $order_statuses){
        $data = array();
        
        if(!empty($customer_ids) && !empty($order_statuses)){
            $customer_ids = array_unique(array_filter(array_map('intval', $customer_ids), function($v) {return $v > 0;}));
            $sql = "
                    SELECT
                        o.order_id,
                        o.order_no,
                        o.customer_id,
                        CONCAT(o.firstname,' ', o.lastname) AS customer_name,
                        GROUP_CONCAT(osub.suborder_id ) AS suborder_id,
                        osub.order_status_id
                    FROM
                        ".DB_PREFIX."order AS o
                    INNER JOIN 
                        ".DB_PREFIX."suborder AS osub ON o.order_id = osub.order_id
                    WHERE
                        o.store_id IN (". WSB_STORES_ID .")
                        AND o.stock_transfer = 0
                        AND o.franchise_id = 0 
                        AND o.total > 0
                        AND o.customer_id IN (". implode(',', $customer_ids).")
                        AND osub.order_status_id IN (". implode(',', $order_statuses) .")
                    GROUP BY
                        o.order_id
                   ";
            $qry = $db->query($sql);
            if($qry->num_rows > 0){
                $data = $qry->rows;
            }
        }

        return $data;
    }


    /**
     * @info: Public static method to get order related information for given customer_id, 
     *        To show customer profile information,
     * @param: DB object- $db,
     * @param: int - $customer_id,
     * @return: array
     *           Keys:- first_order_date, (First order date, placed by customer, which must not be missing/cancelled order)
     *                  total_order_amount, (Sum of all order's total placed by customer)
     *                  total_credit_order_amount, (Sum of all orders total with payment_code 'wsb_credit')
     *                  credit_order_number (count of all 'wsb_credit' orders)
     * @author: Nishu, 24th June 2019
     *
    */
    public static function getOrderDetailsForCustomerProfile($db, int $customer_id): array{
        $data = array();
        if(!empty($customer_id)){

            $sql = "
                    SELECT 
                        ROUND(TIMESTAMPDIFF(DAY, MIN(DATE(o.date_added)), CURDATE())*12/365.24, 1) AS wsb_vintage,
                        ROUND(TIMESTAMPDIFF(DAY, MIN(DATE(IF(o.payment_code ='wsb_credit', o.date_added, CURDATE()))), CURDATE())*12/365.24, 1) AS credit_vintage,
                        COALESCE(SUM(o.total) , 0) AS order_total,
                        COALESCE(SUM(IF(o.payment_code ='wsb_credit', o.total, 0) ) , 0) AS total_credit_orders,
                        
                        COUNT(
                            IF(o.payment_code ='wsb_credit' AND FIND_IN_SET(". ORDER_STATUS['Failed'].", osub.order_statuses) , o.order_id, NULL) 
                        ) AS total_failed_credit_orders,
                        COUNT(IF(o.payment_code ='wsb_credit', 1, NULL) ) AS count_credit_orders
                    FROM
                        ". DB_PREFIX ."order AS o
                    INNER JOIN
                        (
                            SELECT
                                order_id,
                                GROUP_CONCAT(DISTINCT order_status_id) AS order_statuses
                            FROM
                                ".DB_PREFIX."suborder
                            WHERE
                                order_status_id > 0 AND order_status_id != 2
                            GROUP By
                                order_id
                        )
                         AS osub ON o.order_id = osub.order_id
                        
                    WHERE
                        o.store_id IN (". WSB_STORES_ID .")
                        AND o.stock_transfer = 0
                        AND o.franchise_id = 0 
                        AND o.total > 0
                        AND o.customer_id = ". (int)$customer_id ."
                   ";
            
            $result = $db->query($sql);
            if($result->num_rows > 0){
                $data = $result->row;
            }
        }

        return $data;
    }

        /**
     * Method to get total advance for a given order_ids
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $order_ids - Order ID Array (array, required)
     * @return: Total advance values (array)
     * @warning: This method will work only for order code_version >= 2.0
     * @author Devendra, Sep 2019
     */
    public static function getTotalAdvanceByOrderIds($db, $order_ids, $include_wsb_credit = true) {
        $total_advance_arr = array();
        if (empty($order_ids)) return array();

        foreach($order_ids as $order_id) {
            $total_advance_arr[$order_id] = 0;
        }

        // Get total advance for the given Order IDs
        $sql = "SELECT SUM(amount) as advance, order_id 
                FROM " . DB_PREFIX . "order_payment
                WHERE order_id IN (" . implode(",", $order_ids) . ")
                  AND successfull = 1
                  AND amount > 0 ";
                  
        if (!$include_wsb_credit) {
            $sql .= " AND payment_gateway <> 'wsb_credit' ";
        }

        $sql .= " GROUP BY order_id";

        $result = $db->query($sql);

        if ($result->num_rows) {
            foreach($result->rows as $row) {
                $total_advance_arr[$row['order_id']] = (float) ($row['advance']);
            }
        }

        return $total_advance_arr;
    }


}

// close OrderInfo class
?>
