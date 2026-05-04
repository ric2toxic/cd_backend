<?php
/**
 * order edit class for editing order
 * Mostly Contains Static functions
 *
 */
class OrderEdit {

    /**
     * [editOrder is for editing an order.]
     * @param  [object] $db            instance of DB class
     * @param  [array] $original_data   original order snapshot
     * @param  [array] $changes       [array of changes]
     *
     */
    public static $_field_for_adjust_totals = array(
        'order' => array(
        ),
        'order_product' => array(
            'price_per_piece',
            'quantity',
            ''
        )
    );

    public static function saveEditHistory($db, $data) {
        $sql = "INSERT INTO " . DB_PREFIX . "order_edit_history
                 SET order_id         = '" . (int) $data['order_id'] . "' ,
                     suborder_id      = '" . $db->escape($data['suborder_id']) . "' ,
                     key_id           = '" . (!empty($data['key_id']) ? (int) $data['key_id'] : 0 ) . "' ,
                     key_name         = '" . (!empty($data['key_name']) ? $db->escape($data['key_name']) : '') . "' ,
                     edit_type        = '" . $db->escape($data['edit_type']) . "' ,
                     field_name       = '" . (!empty($data['field_name']) ? $db->escape($data['field_name']) : '') . "' ,
                     old_value        = '" . $db->escape($data['old_value']) . "' ,
                     new_value        = '" . $db->escape($data['new_value']) . "' ,
                     comment          = '" . $db->escape($data['comment']) . "' ,
                     user_id          = '" . (int) $data['user_id'] . "' ,
                     name             = '" . $db->escape($data['name']) . "' ,
                     user_name        = '" . $db->escape($data['user_name']) . "' ,
                     date_added       =  NOW() ";
        $db->query($sql);
    }

    public static function editOrder($controller, $order_id, $data) {
        if (!empty($data)) {
            $db = $controller->db;
            if (!empty($data['payment_code'])) {

                $payment_code = $data['payment_code']['new'];
                $payment_method = 'Cash On Delivery';
                if ($payment_code == 'credit') {
                    $payment_method = 'Credit';
                } else if ($payment_code == 'wsb_credit') {
                    $payment_method = 'WholesaleBox Credit (Pay Later Scheme)';
                } else if ($payment_code == 'mswipe_credit') {
                    $payment_method = 'Mswipe Credit';
                } else if ($payment_code == 'udaan_credit') {
                    $payment_method = 'Udaan Credit';
                } else if ($payment_code != 'cod') {
                    $payment_method = 'Prepaid';
                }
                $sql = "UPDATE " . DB_PREFIX . "order
                         SET payment_code = '" . $db->escape($payment_code) . "',
                             payment_method = '" . $db->escape($payment_method) . "'
                         WHERE order_id = '" . (int) $order_id . "'";
                $result = $db->query($sql);
                if ($result) {
                    if ($payment_code == 'cod' || in_array($payment_code, CREDIT_PAYMENT_CODES) || ($payment_code != 'cod' && !empty($data['apply_discounts']))) {
                        self::refreshOrCustomPaychargeDiscount($controller, $order_id, '', $data);
                    } else {
                      // check if customer have membership
                      // call this method with fifth parametetr( apply only membership discount )
                      self::refreshOrCustomPaychargeDiscount($controller, $order_id, '', $data, true);
                    }
                    // Commented By Nilesh as new logic implemented triggers remove
                    //$db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");
                    $history = array(
                        'order_id' => $order_id,
                        'suborder_id' => '',
                        'edit_type' => 'PAYMENT_CODE',
                        'old_value' => $data['payment_code']['old'],
                        'new_value' => $payment_code,
                        'comment' => '',
                        'user_id' => $data['user_id'],
                        'name' => $data['name'],
                        'user_name' => $data['user_name']
                    );
                    self::saveEditHistory($db, $history);
                }
            }
        }
    }

    public static function removeOrAddPaychargeDiscount($controller, $order_id, $data = array(), $check_customer_paycharge_discount = false) {
        $db = $controller->db;
        $selector = array(
            'order' => array(
                'select' => array(
                    'customer_id',
                    'payment_code'
                ),
            ),
            'order_product' => array()
        );
        $order_info = OrderInfo::getOrderInfo($db, $order_id, '', $selector);
        $suborder_info = $order_info['suborder'];
        $order_info = $order_info['order'];


        $product_info = array();
        $order_wise_product_info = array();
        array_walk($suborder_info, function( $suborder, $key ) use ( &$product_info, &$order_wise_product_info ) {
            $product_info[$key] = $suborder['order_product'];
            $order_wise_product_info = array_merge($order_wise_product_info, $suborder['order_product']);
        });
        $overall_subtotal = 0;
        array_walk($order_wise_product_info, function(&$value, $key) use(&$overall_subtotal) {
            $overall_subtotal += (float) $value['price_per_piece'] * (int) $value['quantity'] * (int) $value['piece_in_set'];
        });


        require_once(DIR_SYSTEM . 'library/total/paycharge.php');

        $paychange = new Paycharge($controller);
        $paychange->setOptions('custom_call', true);
        $paychange->setOptions('customer_id', $order_info['customer_id']);
        $paychange->setOptions('sub_total', $overall_subtotal);
        $paychange->setOptions('payment_method_code', $order_info['payment_code']);
        $paychange->setOptions('cart_products', $order_wise_product_info);
        $paychange->setOptions('cart_data', $order_wise_product_info);
        $discount_percent = $paychange->getPaychargeDiscountPercent();

        foreach ($order_wise_product_info as $order_product) {
            $update_flag = false;
            if ($discount_percent['discount_percent'] == 0 || $check_customer_paycharge_discount) {
                $discount_per_piece = 0;
                if (!empty($order_product['discount_breakup'])) {
                    $discount_breakup = unserialize($order_product['discount_breakup']);
                    unset($discount_breakup['paycharge']);
                    foreach ($discount_breakup as $discount) {
                        $discount_per_piece += (float) $discount['value'];
                    }
                    $data = array(
                        'discount_per_piece' => "$discount_per_piece",
                        'discount_breakup' => serialize($discount_breakup),
                    );
                    $update_flag = true;
                }
            } else {
                $discount_breakup = array();
                if (!empty($order_product['discount_breakup'])) {
                    $discount_breakup = unserialize($order_product['discount_breakup']);
                }
                $discount_breakup['paycharge']['payment_method'] = $discount_percent['paychange_data']['payment_method'];
                $discount_breakup['paycharge']['valuep'] = $discount_percent['paychange_data']['valuep'];
                $discount_breakup['paycharge']['amount'] = $discount_percent['paychange_data']['amount'];
                $discount_per_piece = round(
                        $order_product['price_per_piece'] / 100 * (float) $discount_percent['discount_percent'], (int) $controller->currency->getDecimalPlace()
                );
                $discount_breakup['paycharge']['value'] = "-$discount_per_piece";
                $discount_per_piece = 0;

                foreach ($discount_breakup as $discount) {
                    $discount_per_piece += (float) $discount['value'];
                }
                $data = array(
                    'discount_per_piece' => "$discount_per_piece",
                    'discount_breakup' => serialize($discount_breakup),
                );
                $update_flag = true;
            }
            if ($update_flag) {
                self::updateOrderProduct($db, $order_product['order_product_id'], $data);
            }
        }
    }

    /*
     * Function getOrderProductByOPId is used to get all information of order product by order product id
     * @params: $db= db object(array) , $order_product_id (int)
     * @author: NILESH, 2018
     */

    public static function getOrderProductByOPId($db, $order_product_id) {
        $sql = "SELECT op.order_product_id,
                       op.order_id,
                       op.suborder_id,
                       op.product_id,
                       op.buyer_invoice_id,
                       op.name,
                       op.model, 
                       op.hsn_code,
                       op.quantity,
                       op.piece_in_set,
                       op.price_per_piece,
                       op.discount_per_piece,
                       op.discount_breakup,
                       op.weight_per_piece,
                       op.tax,
                       op.output_tax_rates,
                       op.comment,
                       op.seller_id,
                       op.unit_id,
                       op.seller_invoice_id,
                       op.seller_sku,
                       op.transfer_price_per_piece,
                       op.seller_input_tax,
                       op.store_sales,
                       op.store_pickup,
                       op.customer_comment,
                       op.edit_type,
                       op.edit_history,
                       op.last_modified,
                       op.pickup_status,
                       op.pickup_last_modified,
                       op.pickup_history,
                       op.sor_product,
                       op.sor_payment_id,
                       op.wsb_purchase_id,
                       op.is_returnable,
                       op.franchise_id,
                       op.notes,
                       op.combo_product_id
                FROM 
                    " . DB_PREFIX . "order_product AS op
                WHERE 
                    op.order_product_id = '" . (int) $order_product_id . "'";
        $result = array();
        $query = $db->query($sql);
        if ($query->num_rows) {
            $result = $query->row;
            $sql_option = "SELECT *
                            FROM " . DB_PREFIX . "order_option
                            WHERE order_product_id = '" . (int) $order_product_id . "' LIMIT 1";
            $option_query = $db->query($sql_option);
            if ($option_query->num_rows) {
                $result['order_option'] = $option_query->row;
            }
        }
        return $result;
    }

    /**
     * @info: Function splitOrderProduct used for splitting order products, without changing piece in set of order_product
     * When Suborder is revived, and we updating product's stock by up/down quantity
     * @param: $db, array $data 
     * @return: Returns newly added order_product_id
     * @author: Nishu, Nov 2018
     */

    public static function splitOrderProductWithoutEffectPieceInSet($db, $data) {
        
        $new_opid         = 0;
        $order_product_id = $data['order_product_id'];
        //getting information of order product
        $product_data = self::getOrderProductByOPId($db, $order_product_id);

        if (empty($product_data) ) {
            return false;
        }

        if ( !empty($data['quantity']) && $data['quantity'] < $product_data['quantity']) {
            $key_in = array(
                'order_product_id' => $order_product_id,
                'quantity'         => $product_data['quantity'],
                'edit_type'        => $product_data['edit_type'],
                'last_modified'    => $product_data['last_modified'],
                'edit_history'     => $product_data['edit_history']
            );

            $edit_history = array();

            if (!empty($product_data['edit_history'])) {
                $edit_history = unserialize($product_data['edit_history']);
            }
            
            $new_edit_history['user_type']  = 'Operations';
            $new_edit_history['user_ip']    = getClientIpAddress();
            $new_edit_history['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $new_edit_history['date_added'] = date('Y-m-d');
            $new_edit_history['comment']    = $data['edit_type'];
            
            $edit_history[] = $new_edit_history;
            $edit_history = serialize($edit_history);

            $not_reqd_quantity = (int)$product_data['quantity'] - (int) $data['quantity'];
         
            $key_out = array(
                array(
                    'order_product_id' => $order_product_id,
                    'quantity'         => (int) $data['quantity'],
                    //in case of old_edit_type means SELLER_PARTIAL else as is it (returns should be in else part)
                    'edit_type'        => $product_data['edit_type'],
                    'last_modified'    => DATE('Y-m-d H:i:s'),
                    'edit_history'     => $old_edit_history
                ),
                array(
                    'order_product_id' => $order_product_id,
                    'quantity'         => $not_reqd_quantity,
                    'edit_type'        => $data['edit_type'],
                    'last_modified'    => DATE('Y-m-d H:i:s'),
                    'edit_history'     => $edit_history
                )
            );

            $skip_fields = array('order_product_id');
            // Copy order_product row to another for new order_product_id 
            $new_opid = $db->copyRow(DB_PREFIX . 'order_product', $key_in, $key_out, true, $skip_fields);

            if (!empty($product_data['order_option']) && $new_opid) {
                $key_in = array('order_product_id' => $order_product_id);
                $key_out = array();
                $key_out[] = array('order_product_id' => $new_opid);
                $skip_fields = array('order_option_id');
                // Copy order_option row to another for new order_product_id 
                $db->copyRow(DB_PREFIX . 'order_option', $key_in, $key_out, false, $skip_fields);
            }
        } 

        return $new_opid;
    }

    /*
     * Function splitOrderProduct used for splitting order products. Generally for 4 cases
     * Case 1: Seller Panel website
     * Case 2: Seller Panel App
     * Case 3: From Returns
     * Case 4: From khufiya_vibhag
     * When seller or admin mark seller_partial than this function will split that order product in two parts one is seller_partial and another is not-supplied same for returns some parts given and some are updated with new reason(edit_type)
     * @params: $db - db object(array), 
     * $order_product_id - order product id(int), 
     * $data - array(required fields)
     * $seller_invoice_id (not required) (int) default 0
     * $is_only_set_remove (boolean) default false (for partial on sets)
     * @return: true if successfull else false
     * @author: NILESH, 2018
     */

    public static function splitOrderProduct($db, $order_product_id, $data, $seller_invoice_id = 0, $is_only_set_remove = false) {
        $last_id = $order_product_id;
        //getting information of order product
        $product_data = self::getOrderProductByOPId($db, $order_product_id);

        if (empty($product_data) || empty($data['edit_type'])) {
            return false;
        }
        $edit_history = array();
        $old_edit_history = array();
        if (!empty($product_data['edit_history'])) {
            $edit_history = unserialize($product_data['edit_history']);
        }
        if (isset($data['edit_history'])) {
            $edit_history[] = $data['edit_history'];
        }

        //calculating total pieces
        $total_pieces = (int) $product_data['quantity'] * (int) $product_data['piece_in_set'];
        if (isset($data['quantity']) && $data['quantity'] > 0 && $data['quantity'] < $total_pieces) {
            $key_in = array(
                'order_product_id' => $order_product_id,
                'quantity' => $product_data['quantity'],
                'piece_in_set' => $product_data['piece_in_set'],
                'edit_type' => $product_data['edit_type'],
                'last_modified' => $product_data['last_modified'],
                'edit_history' => $product_data['edit_history']
            );
            $old_edit_history = $edit_history;
            if (!empty($edit_history)) {
                $edit_history = serialize($edit_history);
            }
            //not required for returns
            if (!empty($data['old_edit_type'])) {
                if (isset($data['edit_history'])) {
                    $old_edit_history[] = $data['edit_history'];
                }

                $old_edit_history[count($old_edit_history) - 1]['comment'] = $data['old_edit_type'];
            }
            if (!empty($old_edit_history)) {
                $old_edit_history = serialize($old_edit_history);
            }

            $piece_in_set_update = 1;
            $not_reqd_quantity = ((int) $total_pieces - (int) $data['quantity']);
            $not_reqd_piece_in_set_update = 1;
            if ($is_only_set_remove) {
                $not_reqd_quantity = MAX(((int) $product_data['quantity'] - (int) $data['quantity']), 0);
                $piece_in_set_update = (int) $product_data['piece_in_set'];
                $not_reqd_piece_in_set_update = (int) $product_data['piece_in_set'];
            }

            $key_out = array(
                array(
                    'order_product_id' => $order_product_id,
                    'quantity' => (int) $data['quantity'],
                    'piece_in_set' => $piece_in_set_update,
                    //in case of old_edit_type means SELLER_PARTIAL else as is it (returns should be in else part)
                    'edit_type' => !empty($data['old_edit_type']) ? $data['old_edit_type'] : $product_data['edit_type'],
                    'last_modified' => DATE('Y-m-d H:i:s'),
                    'edit_history' => $old_edit_history
                ),
                array(
                    'order_product_id' => $order_product_id,
                    'quantity' => $not_reqd_quantity,
                    'piece_in_set' => $not_reqd_piece_in_set_update,
                    'edit_type' => $data['edit_type'],
                    'last_modified' => DATE('Y-m-d H:i:s'),
                    'edit_history' => $edit_history
                )
            );

            if ($data['edit_type'] == 'YES' ||
                    $data['edit_type'] == 'SELLER_LATER_DISPATCH' ||
                    $data['edit_type'] == 'SELLER_NOT_SUPPLIED') {
                $key_in['seller_invoice_id'] = $product_data['seller_invoice_id'];
                $x = 0;
                foreach ($key_out as $key => &$value) {
                    if ($x == 0) {
                        //in case of new seller_invoice_id generation the value of seller_invoice_id should be $seller_invoice id 
                        $value['seller_invoice_id'] = !empty($seller_invoice_id) ? $seller_invoice_id : $product_data['seller_invoice_id'];
                    } else {
                        $value['seller_invoice_id'] = NULL;
                    }
                    $x++;
                }
            }

            $skip_fields = array('order_product_id');
            // Copy order_product row to another for new order_product_id 
            $last_id = $db->copyRow(DB_PREFIX . 'order_product', $key_in, $key_out, true, $skip_fields);
            if (!empty($product_data['order_option']) && $last_id) {
                $key_in = array('order_product_id' => $order_product_id);
                $key_out = array();
                $key_out[] = array('order_product_id' => $last_id);
                $skip_fields = array('order_option_id');
                // Copy order_option row to another for new order_product_id 
                $db->copyRow(DB_PREFIX . 'order_option', $key_in, $key_out, false, $skip_fields);
            }
        } else if (isset($data['quantity']) && $data['quantity'] == 0) {

            $data_to_update = array(
                'edit_type' => $data['edit_type'],
                'order_id' => $product_data['order_id'],
                'suborder_id' => $product_data['suborder_id']
            );
            if (!empty($edit_history)) {
                $data_to_update['edit_history'] = $edit_history;
            }
            self::updateOrderProduct($db, $order_product_id, $data_to_update);
        }

        if ($is_only_set_remove) {
            return $last_id;
        }

        //Update seller product stock will be only if $data['edit_type'] in $edit_type_check
        $edit_type_check = array('SELLER_NOT_SUPPLIED', 'REJECTED_WRONG_PRODUCT', 'REJECTED_SELLER_DAMAGE');
        if (in_array($data['edit_type'], $edit_type_check)) {
            //If options in this product
            $option_arr = array();
            if (!empty($product_data['order_option']['product_option_value_id'])) {
                $option_arr['product_option_value_id'] = $product_data['order_option']['product_option_value_id'];
            }

            //As per new requirement stock out is not required for purchase inventory 
            $is_seller_can_invoice = SellerInfo::checkSellerInvoiceToBeGenerated($db, $product_data['seller_id']);
            if (!empty($is_seller_can_invoice)) {
                //Now updating seller product stock to zero if subtract = 1
                self::updateSellerStock($db, $product_data['product_id'], $product_data['seller_id'], $option_arr);
            }
        }
        return $last_id;
    }

    public static function updateOrderProduct($db, $order_product_id, $product_data) {
        if (!empty($product_data)) {
            $sub_sql = '';
            $where_sql = '';
            $sql = "UPDATE " . DB_PREFIX . "order_product SET ";
            $query = array();
            foreach ($product_data as $fields => $value) {
                if ($fields != 'order_product_id') {
                    if ($fields == 'edit_history') {
                        $value = serialize($value);
                    }
                    $query[] = " $fields = '" . $db->escape($value) . "' ";
                    if ($fields == 'edit_type' && ($value == 'YES' || $value == 'SELLER_NOT_SUPPLIED' || $value == 'SELLER_LATER_DISPATCH')) {
                        $sub_sql = ",seller_invoice_id = NULL ";
                    }
                    if ($fields == 'pickup_status' && $value == 'Received') {
                        $where_sql .= " AND seller_invoice_id > 0 ";
                    }
                }
            }
            if (!empty($query)) {
                $sql .= implode(",", $query);
                $sql .= $sub_sql;
                $sql .= ", last_modified = NOW() ";
                $sql .= "WHERE order_product_id = '" . (int) $order_product_id . "' ". $where_sql;
                if ($db->query($sql)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Function updateProductStock is used for updating product stock while changing edit_type as cancelled_by_customer or on suborder cancelation
     * @params: $db- database obj
     *          $product_id (int)
     *          $seller_id (int)
     * @author: NILESH, 2018
     */
    public static function updateProductStock($db, $product_id, $seller_id, $quantity = 0, $option_data = array()) {

        $product_info = self::getProductDetails($db, $product_id);
        if (!empty($product_info['subtract'])) {

            if (!empty($option_data)) {

                // Stocking out the particular option
                $sql = "UPDATE " . DB_PREFIX . "product_option_value
                        SET quantity = quantity+" . (int) $quantity . "
                        WHERE product_option_value_id = '" . (int) $option_data['product_option_value_id'] . "' AND 
                              subtract = '1'";
                $db->query($sql);

                self::updateOptionProducts($db, $product_id);
            } else {
                $sql_update_product = "UPDATE " . DB_PREFIX . "product
                                       SET quantity = quantity+" . (int) $quantity . "
                                       WHERE product_id = '" . (int) $product_id . "'";
                $db->query($sql_update_product);
            }

            //Updating if there is assoiate product then update combo product 
            Product::updateComboProductQuantityUsingAssociate($db, $product_id);

            $data['product_ids'] = array($product_id);
            self::insertSellerChangeLog($db, $data, $seller_id);
            return true;
        }
    }

    /**
     * Function updateSellerStock is used for updating seller product stock and setting it to zero
     * @params: $db- database obj
     *          $product_id (int)
     *          $seller_id (int)
     * @author: NILESH, 2018
     */
    public static function updateSellerStock($db, $product_id, $seller_id, $option_data = array()) {
        // fetching value of subtract from product table and check that value is 0 or 1.
        // if subtract has 0 that means quantity will never 0.
        // if subtract has 1 then check with piece_in_set is greater then 1, so quantity will 0 other wise will never 0. 
        $product_info = self::getProductDetails($db, $product_id);
        if (!empty($product_info['subtract'])) {

            if (!empty($option_data)) {

                // Stocking out the particular option
                $sql = "UPDATE " . DB_PREFIX . "product_option_value
                        SET quantity = 0
                        WHERE product_option_value_id = '" . (int) $option_data['product_option_value_id'] . "' AND 
                              subtract = '1'";
                $db->query($sql);

                // when seller not given to goods then perticular product quantity will sum of all remaining options quantity
                self::updateOptionProducts($db, $product_id);
            } else {
                // when seller not given to goods then perticular product quantity will 0
                $sql_update_product = "UPDATE " . DB_PREFIX . "product
                                       SET quantity = 0,
                                           date_out_of_stock = NOW()
                                       WHERE product_id = '" . (int) $product_id . "'";
                $db->query($sql_update_product);
            }

            //Updating if there is assoiate product then update combo product 
            Product::updateComboProductQuantityUsingAssociate($db, $product_id);

            $data['product_ids'] = array($product_id);
            self::insertSellerChangeLog($db, $data, $seller_id);
            return true;
        }
    }

    /**
     * Function updateOptionProducts is used for update option products oc_product table
     * @params: $db- database obj
     *          $product_id (int)
     * @author: NILESH, 2018
     */
    public static function updateOptionProducts($db, $product_id) {

        $sql = "UPDATE
                    oc_product op
                SET
                    op.quantity =(
                        SELECT
                            SUM(quantity)
                        FROM
                            oc_product_option_value opov
                        WHERE
                            opov.product_id = op.product_id
                    )
                WHERE
                    op.product_id = '" . (int) $product_id . "'";
        $db->query($sql);
    }

    /**
     * 
     */
    public static function getProductDetails($db, $product_id) {
        $sql_pro_info = "SELECT subtract,
                                piece_in_set
                         FROM " . DB_PREFIX . "product
                         WHERE product_id = '" . (int) $product_id . "'";
        $query_pro_query = $db->query($sql_pro_info);
        return $query_pro_query->row;
    }

    /**
     * Method to seller change log
     * @param: $db: Object of Database   
     * @param: $data: array of data   
     * @param: $seller_id : Integer of seller Id   
     * @return NULL
     * @author Vikas, 2017
     */
    public static function insertSellerChangeLog($db, $data, $seller_id) {
        $seller_nick_name = SellerInfo::getSellerFirmDetails($db, $seller_id)['nickname'];
        $oop_sql = "SELECT product_id,
                       quantity,
                       sku
                FROM " . DB_PREFIX . "product 
                WHERE product_id IN (" . implode(',', $data['product_ids']) . ")";
        $oop_query = $db->query($oop_sql);

        if ($oop_query->num_rows) {
            foreach ($oop_query->rows as $key => $values) {
                $sql = "INSERT INTO " . DB_PREFIX . "seller_change_log 
                SET seller_id = '" . $seller_id . "',
                    product_id = '" . (int) $values['product_id'] . "',
                    product = '" . $db->escape($values['sku']) . "',
                    nickname = '" . $db->escape($seller_nick_name) . "',
                    updated_type = '" . $db->escape('set_quantity') . "',
                    old = '" . (int) $values['quantity'] . "',
                    new = 0 ,
                    modified = NOW() ";
                $db->query($sql);
            }
        } else {
            return false;
        }
    }

    /**
     * Method to Get suborder products weight 
     * @param: $db: Object of Database   
     * @param: $order_id: order_id   
     * @param: $suborder_id: suborder_id   
     * @return weight of suborder products
     * @author Nilesh, 2017
     */
    public static function getProductWeightForShipping($db, $order_id, $suborder_id) {
        $query_for_suborder = $db->query("SELECT buyer_invoice_id 
                                        FROM oc_suborder os
                                        WHERE os.order_id = '" . (int) $order_id . "' AND
                                              os.suborder_id = '" . $db->escape($suborder_id) . "'");

        if ($query_for_suborder->num_rows) {
            $buyer_invoice_id = (int) $query_for_suborder->row['buyer_invoice_id'];
        } else {
            $buyer_invoice_id = 0;
        }

        $sql = "SELECT SUM(oop.piece_in_set * oop.quantity * oop.weight_per_piece) as weight 
                FROM oc_order_product oop 
                WHERE oop.order_id = '" . (int) $order_id . "' AND
                      oop.suborder_id = '" . $db->escape($suborder_id) . "' AND";
        if ($buyer_invoice_id > 0) {
            $sql .= " oop.buyer_invoice_id = '" . $buyer_invoice_id . "' ";
        } else {
            $sql .= " oop.edit_type IN ('YES', 
                        'DAMAGE_BY_COURIER_COMPANY',
                        'SELLER_APPROVED',
                        'SELLER_PARTIAL',
                        'SELLER_LATER_DISPATCH') ";
        }

        $sql .= "GROUP BY oop.suborder_id";

        return (!empty($db->query($sql)->row['weight'])) ? ((float) $db->query($sql)->row['weight']) : 0;
    }

    public static function editOrderShipping($db, $order_id, $suborder_id, $data) {
        if (!empty($data)) {
            if (!empty($data['shipping_code']) && !empty($data['shipping_method']) && isset($data['new_shipping_value'])) {
                $shipping_code = $data['shipping_code']['new'];
                $shipping_method = $data['shipping_method']['new'];


                $history = array(
                    'order_id' => $order_id,
                    'suborder_id' => $suborder_id,
                    'edit_type' => 'SHIPPING_CODE',
                    'old_value' => $data['shipping_code']['old'] . '~' . $data['shipping_method']['old'],
                    'new_value' => $shipping_code . '~' . $shipping_method,
                    'comment' => '',
                    'user_id' => $data['user_id'],
                    'name' => $data['name'],
                    'user_name' => $data['user_name']
                );
                self::saveEditHistory($db, $history);
                if ($data['apply_ess_charges']) {
                    $history = array(
                        'order_id' => $order_id,
                        'suborder_id' => $suborder_id,
                        'edit_type' => 'APPLY_ESS_SHIPPING',
                        'old_value' => 0,
                        'new_value' => $data['ess_charges'],
                        'comment' => '',
                        'user_id' => $data['user_id'],
                        'name' => $data['name'],
                        'user_name' => $data['user_name']
                    );
                    self::saveEditHistory($db, $history);
                    $suborder_history = OrderInfo::getLastSuborderHistoryInfo($db, $order_id, $suborder_id);
                    //User Details
                    $user_details = !empty($edit_history['name']) ? $edit_history['name'] : '';
                    $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $suborder_history['order_status_id'] . "', 'ESS charges Applied: " . $data['ess_charges'] . "', '" . $db->escape($data['users_name']) . "',  '" . date('Y-m-d H:i:s') . "')");
                }
                $result = self::changeShippingCharge($db, $data['new_shipping_value'], $order_id, $suborder_id, $shipping_code, $shipping_method);
            }
        }
    }

    public static function editOrderStockTransfer($db, $order_id, $data) {


        if (!empty($data)) {

            $history = array(
                'order_id' => $order_id,
                'suborder_id' => '',
                'edit_type' => 'STOCK_TRANSFER',
                'old_value' => 0,
                'new_value' => 1,
                'comment' => '',
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'user_name' => $data['user_name']
            );
            self::saveEditHistory($db, $history);

            $db->query("UPDATE " . DB_PREFIX . "order_product 
                        SET discount_per_piece = 0, 
                            discount_breakup   = 0, 
                            price_per_piece    = ROUND(transfer_price_per_piece/ (1 + seller_input_tax/100), 2), 
                            output_tax_rates   = seller_input_tax  
                        WHERE order_id     = '" . (int) $order_id . "'");

            $db->query("UPDATE " . DB_PREFIX . "order 
                        SET   stock_transfer = 1 
                        WHERE order_id = '" . (int) $order_id . "'");

            return true;
        }
    }

    /**
     * Function updateToStockTransfer used for update any order to stock_transfer according to params
     * @param $db - database obj (array),
     * $order_id - order_id required (int),
     * $data - for use to update order_product as suborder_id or order_product_id wise (array)
     * @return true if successful else false
     * @author Nilesh, 2018
     */
    public static function updateToStockTransfer($db, $order_id, $data) {

        $sql = "UPDATE " . DB_PREFIX . "order_product 
                SET discount_per_piece = 0, 
                    discount_breakup   = NULL, 
                    price_per_piece    = ROUND(transfer_price_per_piece/ (1 + seller_input_tax/100), 2), 
                    output_tax_rates   = seller_input_tax  
                WHERE order_id = '" . (int) $order_id . "' ";
        if (!empty($data['suborder_id'])) {
            $sql .= " AND suborder_id = '" . $db->escape($data['suborder_id']) . "'";
        } elseif (!empty($data['order_product_id'])) {
            $sql .= " AND order_product_id IN (" . implode(',', $data['order_product_id']) . ")";
        }
        if ($db->query($sql)) {
            self::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id, true);
            //Now update order table stock transfer field
            $db->query("UPDATE " . DB_PREFIX . "order 
                        SET stock_transfer = 1 
                        WHERE order_id = '" . (int) $order_id . "'");

            //Insert history
            $history = array(
                'order_id' => $order_id,
                'suborder_id' => (!empty($data['suborder_id']) ? $data['suborder_id'] : ''),
                'edit_type' => 'STOCK_TRANSFER',
                'old_value' => 0,
                'new_value' => 1,
                'comment' => '',
                'key_name' => 'order_id',
                'key_id' => $order_id,
                'field_name' => 'stock_transfer',
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'user_name' => $data['user_name']
            );
            self::saveEditHistory($db, $history);
            return true;
        } else {
            return false;
        }
    }

    //Change Shipping Charge by vikas (14-06-2016) copied from model/sale/order by nilesh
    public static function changeShippingCharge($db, $shipping_value, $order_id, $suborder_id, $shipping_code = '', $shipping_method = '') {

        $order_info = OrderInfo::getOrderInfo($db, $order_id, '', array('order' => array('select' => array('code_version', 'currency_id'))));

        $current_shipping_charge = 0;
        $gst = 0;

        if ($order_info['order']['code_version'] == '1.0') {
            $sql = "SELECT value as shipping_charge FROM " . DB_PREFIX . "order_total ";
            $sql .= "WHERE order_id = " . (int) $order_id . " AND ";
            $sql .= " code = 'shipping' AND ";
            $sql .= " suborder_id = '" . $db->escape($suborder_id) . "'";
            $query = $db->query($sql);
        } else {
            $sql = "SELECT shipping_charge, gst FROM " . DB_PREFIX . "suborder
                    WHERE order_id = '" . (int) $order_id . "'
                      AND suborder_id = '" . $db->escape($suborder_id) . "'";
            $query = $db->query($sql);
        }

        // suborder_current_shipping_charge
        if ($query->num_rows > 0) {
            $current_shipping_charge = (float) $query->row['shipping_charge'];
            $gst = (float) $query->row['gst'];
        }

        //If ess charges already applied
        $ess_charges = self::getEssShippingCharges($db, $order_id, $suborder_id);
        $shipping_value = $shipping_value + $ess_charges;
        
        // get dummy currency 
        $sql = "SELECT currency_id, value FROM " . DB_PREFIX . "currency 
                WHERE code = '" . $db->escape(DUMMY_INR_CURRENCY) . "'";
        $currency_query = $db->query($sql);

        if ($currency_query->num_rows > 0) {
          $dummy_inr_currency = $currency_query->row;
          // if currency id is dummy inr currency, then we need to multiply shipping charge with dummy inr currency value
          if ($order_info['order']['currency_id'] == $dummy_inr_currency['currency_id']) {
            $shipping_value = $shipping_value * $dummy_inr_currency['value'];
          }
        }
        $diff = $shipping_value - $current_shipping_charge;

        // Update Suborder total
        $sql = "UPDATE " . DB_PREFIX . "suborder
                SET shipping_charge = " . (float) $shipping_value . ",
                    total = total + " . (float) $diff . "";
        if ($shipping_code != '' && $shipping_method != '') {
            $sql .= " , shipping_code ='" . $db->escape($shipping_code) . "' 
                      , shipping_method ='" . $db->escape($shipping_method) . "'
                    ";
        }

        $sql .= " WHERE order_id = '" . (int) $order_id . "'
                  AND suborder_id = '" . $db->escape($suborder_id) . "'";
        $query = $db->query($sql);

        $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
        $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");

        if ($order_info['order']['code_version'] != '1.0') {
            $basesql = "UPDATE " . DB_PREFIX . "order
                    SET ";
            if ($gst) {
                $sql = " shipping_charge = shipping_charge + " . (float) $diff;
            } else {
                $sql = " total = total + " . (float) $diff . ", shipping_charge = shipping_charge + " . (float) $diff;
            }
            $basesql .= $sql . " WHERE order_id = '" . (int) $order_id . "'";
            $query = $db->query($basesql);
            return true;
        } else {

            $sql = "UPDATE " . DB_PREFIX . "order
                    SET total = total + " . (float) $diff .
                    " WHERE order_id = '" . (int) $order_id . "'";
            $query = $db->query($sql);

            $sql = "UPDATE " . DB_PREFIX . "order_total ";
            $sql .= "SET value = value + " . (float) $diff;
            $sql .= " WHERE order_id = '" . (int) $order_id . "' AND ";
            $sql .= " (code = 'shipping' OR code = 'total') AND ";
            $sql .= " suborder_id = '" . $db->escape($suborder_id) . "'";
            $query = $db->query($sql);

            return true;
        }
    }

    public static function getShippingMethodRequestArrayForSuborder($db, $order_id, $suborder_id) {
        $selector = array('order' => array(),
            'suborder' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
        //$order_info['suborder'][$suborder_id]

        $shipping_request = array(
            'zone_id' => $order_info['order']['shipping_zone_id'],
            'country_id' => $order_info['order']['shipping_country_id'],
            'postcode' => $order_info['order']['shipping_postcode'],
            'is_dropshipper' => 0,
            'cartlimitcross' => 1,
            'weight_cart' => self::getProductWeightForShipping($db, $order_id, $suborder_id)
        );
        return $shipping_request;
    }

    public static function getCustomerCreditStatus($db, $customer_id) {

        $sql = "SELECT occ.customer_id 
                FROM " . DB_PREFIX . "customer_credit occ 
                WHERE occ.credit_status=1 AND 
                      occ.customer_id='" . (int) $customer_id . "'";
        $query = $db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    public static function checkOrderGSTIsWSBGST($db, $order_gst_number) {

        $sql = "SELECT ovir.purchase_firm_tin_no 
                FROM " . DB_PREFIX . "vat_input_rules ovir
                WHERE ovir.status=1 AND 
                      ovir.purchase_firm_tin_no='" . $db->escape($order_gst_number) . "'";
        $query = $db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    // updating addresses in order table developed by vikas coppied by nilesh for all edit order will be in same class
    public static function updateEditAdress($db, $data) {

        $order_id = $data['order_id'];
        $suborder_id = $data['suborder_id'];

        $history = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id,
            'edit_type' => 'CHANGE_ADDRESS',
            'old_value' => $data['old_data'],
            'new_value' => serialize($data),
            'comment' => '',
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'user_name' => $data['user_name']
        );
        self::saveEditHistory($db, $history);

        if (!empty($data['payment_address_checked']) && !empty($data['shipping_address_checked'])) {
            $update_query = "UPDATE " . DB_PREFIX . "order SET
                                payment_firstname = '" . $db->escape($data['first_name']) . "',
                                payment_lastname = '" . $db->escape($data['last_name']) . "',
                                telephone = '" . $db->escape($data['telephone']) . "',
                                payment_company = '" . $db->escape($data['company_name']) . "',
                                payment_address_1 = '" . $db->escape($data['address_1']) . "',
                                payment_address_2 = '" . $db->escape($data['address_2']) . "',
                                payment_city = '" . $db->escape($data['city']) . "',
                                payment_postcode = '" . $db->escape($data['postcode']) . "',
                                payment_country = '" . $db->escape($data['country_name']) . "',
                                payment_country_id = '" . $db->escape($data['country']) . "',
                                payment_zone = '" . $db->escape($data['zone_name']) . "',
                                payment_zone_id = '" . (int) $data['zone'] . "',
                                payment_custom_field = '" . $db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "',
                                shipping_firstname = '" . $db->escape($data['first_name']) . "',
                                shipping_lastname = '" . $db->escape($data['last_name']) . "',
                                shipping_company = '" . $db->escape($data['company_name']) . "',
                                shipping_address_1 = '" . $db->escape($data['address_1']) . "',
                                shipping_address_2 = '" . $db->escape($data['address_2']) . "',
                                shipping_city = '" . $db->escape($data['city']) . "',
                                shipping_postcode = '" . $db->escape($data['postcode']) . "',
                                shipping_country = '" . $db->escape($data['country_name']) . "',
                                shipping_country_id = '" . $db->escape($data['country']) . "',
                                shipping_zone = '" . $db->escape($data['zone_name']) . "',
                                shipping_zone_id = '" . (int) $data['zone'] . "',
                                shipping_custom_field = '" . $db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "'
                            WHERE order_id = '" . (int) $data['order_id'] . "'
						";
        } elseif (!empty($data['payment_address_checked'])) {
            $update_query = "UPDATE " . DB_PREFIX . "order SET
                                payment_firstname = '" . $db->escape($data['first_name']) . "',
                                payment_lastname = '" . $db->escape($data['last_name']) . "',
                                telephone = '" . $db->escape($data['telephone']) . "',
                                payment_company = '" . $db->escape($data['company_name']) . "',
                                payment_address_1 = '" . $db->escape($data['address_1']) . "',
                                payment_address_2 = '" . $db->escape($data['address_2']) . "',
                                payment_city = '" . $db->escape($data['city']) . "',
                                payment_postcode = '" . $db->escape($data['postcode']) . "',
                                payment_country = '" . $db->escape($data['country_name']) . "',
                                payment_country_id = '" . $db->escape($data['country']) . "',
                                payment_zone = '" . $db->escape($data['zone_name']) . "',
                                payment_zone_id = '" . (int) $data['zone'] . "',
                                payment_custom_field = '" . $db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "'
                            WHERE order_id = '" . (int) $data['order_id'] . "'
						";
        } elseif (!empty($data['shipping_address_checked'])) {
            $update_query = "UPDATE " . DB_PREFIX . "order SET
                                shipping_firstname = '" . $db->escape($data['first_name']) . "',
                                shipping_lastname = '" . $db->escape($data['last_name']) . "',
                                telephone = '" . $db->escape($data['telephone']) . "',
                                shipping_company = '" . $db->escape($data['company_name']) . "',
                                shipping_address_1 = '" . $db->escape($data['address_1']) . "',
                                shipping_address_2 = '" . $db->escape($data['address_2']) . "',
                                shipping_city = '" . $db->escape($data['city']) . "',
                                shipping_postcode = '" . $db->escape($data['postcode']) . "',
                                shipping_country = '" . $db->escape($data['country_name']) . "',
                                shipping_country_id = '" . $db->escape($data['country']) . "',
                                shipping_zone = '" . $db->escape($data['zone_name']) . "',
                                shipping_zone_id = '" . (int) $data['zone'] . "',
                                shipping_custom_field = '" . $db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "'
                            WHERE order_id = '" . (int) $data['order_id'] . "'
						";
        } else {
            $update_query = "UPDATE " . DB_PREFIX . "order SET
                                shipping_firstname = '" . $db->escape($data['first_name']) . "',
                                shipping_lastname = '" . $db->escape($data['last_name']) . "',
                                telephone = '" . $db->escape($data['telephone']) . "',
                                shipping_company = '" . $db->escape($data['company_name']) . "',
                                shipping_address_1 = '" . $db->escape($data['address_1']) . "',
                                shipping_address_2 = '" . $db->escape($data['address_2']) . "',
                                shipping_city = '" . $db->escape($data['city']) . "',
                                shipping_postcode = '" . $db->escape($data['postcode']) . "',
                                shipping_country = '" . $db->escape($data['country_name']) . "',
                                shipping_country_id = '" . $db->escape($data['country']) . "',
                                shipping_zone = '" . $db->escape($data['zone_name']) . "',
                                shipping_zone_id = '" . (int) $data['zone'] . "',
                                shipping_custom_field = '" . $db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "'
                            WHERE order_id = '" . (int) $data['order_id'] . "'
						";
        }
        $db->query($update_query);
    }

    //Function to get all order history rows from oc_order_history
    public static function getOrderHistoryItem($db, $order_id, $suborder_id) {
        $sql = "SELECT ooh.*, 
                       oos.name as order_status_name 
                FROM oc_order_history ooh INNER JOIN 
                     oc_order_status oos ON ooh.order_status_id=oos.order_status_id 
                WHERE oos.language_id =1 AND
                      ooh.order_id = '" . (int) $order_id . "' AND
                      ooh.suborder_id = '" . $db->escape($suborder_id) . "'
                ORDER BY ooh.order_history_id ASC";

        $result = $db->query($sql);
        if ($result->num_rows) {
            $result_arr = array_combine(array_column($result->rows, 'order_history_id'), $result->rows);
            return $result_arr;
        } else {
            return false;
        }
    }

    //Function to remove order history items from oc_order_history
    public static function removeOrderHistory($db, $order_id, $suborder_id, $data) {
        if (!empty($data)) {
            foreach ($data['history_id_arr'] as $key => $value) {
                $history = array(
                    'order_id' => $order_id,
                    'suborder_id' => $suborder_id,
                    'edit_type' => 'EDIT_HISTORY',
                    'old_value' => serialize($data['old_order_history'][$value]),
                    'new_value' => '',
                    'comment' => (!empty($data['comments']) ? $data['comments'] : ''),
                    'user_id' => $data['user_id'],
                    'name' => $data['name'],
                    'user_name' => $data['user_name']
                );
                self::saveEditHistory($db, $history);
            }
            $suborder_last_history = OrderInfo::getLastSuborderHistoryInfo($db, $order_id, $suborder_id);
            $db->query("DELETE FROM " . DB_PREFIX . "order_history 
                       WHERE order_history_id IN (" . implode(',', $data['history_id_arr']) . ")");
            $order_status = $db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_history
                                        WHERE order_id='" . (int) $order_id . "' AND
                                              suborder_id= '" . $db->escape($suborder_id) . "' 
                                        ORDER  BY order_history_id DESC 
                                        LIMIT 1")->row['order_status_id'];

            //Get dilvered date for given suborder_id (i.e. First time marked dilvered)
            $delivered_date = Suborder::getFirstDeliveredDateForSuborder($db, $order_id, $suborder_id);
            
            //Update suborder status and delivered accordingly updated suborder history
            $set_data = " , delivered_date = NULL ";
            if(!empty($delivered_date)){
                $set_data = " , delivered_date = '".$db->escape($delivered_date)."' ";
            }
            $update_qry = "
                            UPDATE 
                                " . DB_PREFIX . "suborder 
                            SET 
                                order_status_id= '" . (int) $order_status . "' 
                                ". $set_data ."
                            WHERE 
                                order_id='" . (int) $order_id . "' 
                                AND suborder_id='" . $db->escape($suborder_id) . "'";
            $db->query($update_qry);

            if ((int) $suborder_last_history['order_status_id'] !== (int) $order_status) {
                $old_order_status_id = $suborder_last_history['order_status_id'];
                $order_status_id = $order_status;
                $order_product_info = array();
                $order_product_option_info = array();
                $selector = array(
                    'order_product' => array(),
                    'order_option' => array()
                );
                $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
                if (!empty($order_info['suborder'][$suborder_id]['order_product'])) {
                    $order_product_info = $order_info['suborder'][$suborder_id]['order_product'];
                    $order_product_info = array_combine(array_column($order_product_info, 'order_product_id'), $order_product_info);
                }
                if (!empty($order_info['suborder'][$suborder_id]['order_option'])) {
                    $order_product_option_info = $order_info['suborder'][$suborder_id]['order_option'];
                    $order_product_option_info = array_combine(array_column($order_product_option_info, 'order_product_id'), $order_product_option_info);
                }
                self::updateProductStocksByOrderHistory($db, $old_order_status_id, $order_status_id, $order_product_info, $order_product_option_info);
                self::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id);
            }
            return true;
        }
    }

    //Function to remove order history items from oc_order_edit_history
    public static function removeEssCharges($db, $data) {
        if (!empty($data)) {
            $order_edit_his = self::getSuborderAllEditHistoryByEditId($db, $data['history_edit_id_arr']);

            if (!empty($order_edit_his)) {
                foreach ($order_edit_his as $value) {

                    $suborder_history = OrderInfo::getLastSuborderHistoryInfo($db, $value['order_id'], $value['suborder_id']);
                    $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $value['order_id'] . "', '" . $db->escape($value['suborder_id']) . "', '" . (int) $suborder_history['order_status_id'] . "', 'ESS charges Remove: " . $value['new_value'] . ". Comment: " . $db->escape($data['comments']) . "', '" . $db->escape($data['name']) . "',  '" . date('Y-m-d H:i:s') . "')");
                }
            }

            $db->query("DELETE FROM " . DB_PREFIX . "order_edit_history 
                        WHERE edit_id IN (" . implode(',', $data['history_edit_id_arr']) . ")");
            return true;
        }
    }

    /**
     * Public function to update excess_paymnet flag
     * @param:  $db, $order_id, $excess_payment_flag
     * @return: void
     * @author: Nishu, Oct 2017
     */
    public static function updateExcessPayment($db, $order_ids, $excess_payment_flag) {
        $sql = "Update " . DB_PREFIX . "order 
                  SET payment_cleared = '" . $db->escape($excess_payment_flag) . "' 
                  WHERE order_id IN (" . $order_ids . ") ";
        $db->query($sql); //Execute update query
    }

    //Function to get all deleted order history items
    public static function getDeletedOrderHistoryItemOfSuborder($controller, $order_id, $suborder_id) {
        $db = $controller->db;
        $return_arr = array();
        $sql = "SELECT old_value as removed_history,
                       comment as remove_comment,
                       user_id
                FROM " . DB_PREFIX . "order_edit_history
                WHERE order_id='" . (int) $order_id . "' AND
                      suborder_id= '" . $db->escape($suborder_id) . "' AND
                      edit_type = 'EDIT_HISTORY'
                ORDER  BY edit_id DESC";
        $history_query = $db->query($sql);
        if ($history_query->num_rows) {
            $x = 0;
            foreach ($history_query->rows as $value) {
                $return_arr[$x] = unserialize($value['removed_history']);
                $return_arr[$x]['remove_comment'] = $value['remove_comment'];
                $return_arr[$x]['remove_by'] = $controller->user->getUserName($value['user_id'])['name'];
                $x++;
            }
        }
        return $return_arr;
    }

    //Function to get view(HTML) of deleted order history item
    public static function getDeletedOrderHistoryItemOfSuborderHtml($controller, $order_id, $suborder_id) {
        $deleted_history = self::getDeletedOrderHistoryItemOfSuborder($controller, $order_id, $suborder_id);
        $html = '';
        if (!empty($deleted_history)) {

            $html .= '<div class="col-lg-12">
                        <label>
                            <h3>Already Deleted Order History</h3>
                        </label>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="12.5%">Order Status</th>
                                    <th width="20%">Comment</th>
                                    <th width="20%">Notes</th>
                                    <th width="10%">Notify Email</th>
                                    <th width="10%">Notify SMS</th>
                                    <th width="10%">User</th>
                                    <th width="10%">Date Added</th>
                                    <th width="10%">Removed by</th>
                                    <th width="10%">Removed Comment</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach ($deleted_history as $history_item) {
                $html .= '<tr>
                            <td width="12.5%">' . $history_item['order_status_name'] . '</td>
                            <td width="20%">' . $history_item['comment'] . '</td>
                            <td width="20%">' . $history_item['notes'] . '</td>
                            <td width="10%">' . $history_item['notify_email'] . '</td>
                            <td width="10%">' . $history_item['notify_sms'] . '</td>
                            <td width="10%">' . $history_item['user'] . '</td>
                            <td width="10%">' . $history_item['date_added'] . '</td>
                            <td width="10%">' . $history_item['remove_by'] . '</td>
                            <td width="10%">' . $history_item['remove_comment'] . '</td>
                        </tr>';
            }

            $html .= '</tbody>
            </table>
        </div>';
        }
        return $html;
    }

    //Function to get order product details for break into another suborder
    public static function getOrderProductDetailsForAnotherSuborder($db, $products_info, $order_id, $suborder_id) {

        $seller_ids = array_unique(array_column($products_info, 'seller_id'));
        $sql = "SELECT ms.seller_id,
                           ms.nickname,
                           ms.company,
                           c.email,
                           ms.zone_id,
                           ms.pickup_city_code,
                           ms.seller_invoice_generate
                    FROM " . DB_PREFIX . "ms_seller ms
                    INNER JOIN " . DB_PREFIX . "customer c ON c.customer_id = ms.seller_id
                    WHERE ms.seller_id IN (" . implode(',', $seller_ids) . ")";

        $seller_query = $db->query($sql);
        $seller_info['sellers'] = array();

        foreach ($seller_query->rows as $seller) {
            $seller_info[$seller['seller_id']] = array('nickname' => $seller['nickname'],
                'company' => $seller['company'],
                'email' => $seller['email'],
                'zone_id' => $seller['zone_id'],
                'pickup_city_code' => $seller['pickup_city_code'],
                'seller_invoice_generate' => $seller['seller_invoice_generate']
            );
        }

        $result = array();
        foreach ($products_info as $product) {
            $seller_id = $product['seller_id'];
            $seller_invoice_id = $product['seller_invoice_id'];

            $product['nickname'] = $seller_info[$seller_id]['nickname'];
            $product['company'] = $seller_info[$seller_id]['company'];
            $product['email'] = $seller_info[$seller_id]['email'];
            $product['zone_id'] = $seller_info[$seller_id]['zone_id'];
            $product['pickup_city_code'] = $seller_info[$seller_id]['pickup_city_code'];
            $product['seller_invoice_generate'] = $seller_info[$seller_id]['seller_invoice_generate'];

            if (($product['edit_type'] == 'YES' || $product['edit_type'] == 'SELLER_LATER_DISPATCH') && ($seller_invoice_id == 0 || $seller_invoice_id == NULL)) {
                $result['seller_not_invoiced'][$seller_id][] = $product;
            } else if (($product['edit_type'] == 'SELLER_APPROVED' || $product['edit_type'] == 'SELLER_PARTIAL') && $seller_invoice_id > 0) {
                $result['seller_invoiced'][$seller_id][$seller_invoice_id][] = $product;
            }
        }
        array_multisort($result, SORT_DESC);
        return $result;
    }

    /*
     * Function moved from model/sale/order . Now suborder break code will be from here
     * @author: Nilesh
     */

    public static function updateBreakedSuborderProduct($controller, $order_id, $suborder_id, $order_product_id, $edit_history) {

        $db = $controller->db;
        try {
            // Start transaction
            $db->query(" START TRANSACTION ");

            $selector = array(
                'order' => array(
                    'select' => array('order_id')
                ),
                'suborder' => array(
                    'select' => array(
                        'gst',
                        'buyer_invoice_id',
                        'invoice_no',
                        'invoice_date',
                        'invoice_prefix',
                        'order_status_id',
                        'shipping_charge',
                        'shipping_code')
                ),
                'order_product' => array()
            );
            $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
            $order_details = $order_info['order'];
            $suborder_info = $order_info['suborder'][$suborder_id];
            $products = $suborder_info['order_product'];
            unset($suborder_info['order_product']);

            $products = self::filterOrderProducts($products);

            $is_invoice_generated = false;
            if (!empty($suborder_info['invoice_no']) && $suborder_info['invoice_no'] > 0) {
                $is_invoice_generated = true;
            }

            $is_shipping_update = false;
            if (strstr($suborder_info['shipping_code'], 'weight')) {
                $is_shipping_update = true;
            }
            if ($is_shipping_update) {
                $readjusted_shipping = self::getReadjustedShippingForBreakSuborderIntoAnother($products, $order_product_id, (float) $suborder_info['shipping_charge']);
            }

            $new_suborder_id = self::getNewSuborderId($db, $order_id, $suborder_id);

            self::_copySuborderForBreakSuborderIntoAnother($db, $is_invoice_generated, $suborder_id, $new_suborder_id, $suborder_info);

            self::_copySuborderHistoryForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id);

            self::_copySuborderAdvanceForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $is_invoice_generated);

            if ($is_shipping_update) {
                self::_updateShippingForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $readjusted_shipping);
            }

            $keys_for_log = array();
            if ($is_invoice_generated) {
                $keys_for_log = array(
                    'suborder_id' => $suborder_id,
                    'buyer_invoice_id' => $suborder_info['buyer_invoice_id'],
                    'invoice_no' => $suborder_info['invoice_no'],
                    'invoice_date' => $suborder_info['invoice_date'],
                    'invoice_prefix' => $suborder_info['invoice_prefix']
                );

                //Now update in order_product table's column buyer_invoice_id as NULL by suborder_id because invoice is already generated.
                self::_resetOrderProductBuyerInvoice($db, $order_id, $suborder_id);
            }

            //Now update in order_product table by order_product_id
            self::_changeSuborderIdForOrderProduct($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            //Now calculating suborder total according to split by order_product_id

            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($new_suborder_id) . "')");
            $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");


            // Insert and update order_history in notes containing suborder_id
            //if buyer_invoice_id gets generated the also log this
            $splited_msg = '';
            if ($is_invoice_generated) {
                $splited_msg = "<br>The invoice is being cancelled due to suborder splitting.
                                <br>Invoice No.: " . $keys_for_log['invoice_prefix'] . $keys_for_log['invoice_no'] . "
                                <br>Invoice Date.: " . $keys_for_log['invoice_date'] . "
                                <br>Buyer Invoice ID.: " . $keys_for_log['buyer_invoice_id'] . " ";
            }
            //User Details
            $user_details = !empty($edit_history['name']) ? $edit_history['name'] : '';
            $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $suborder_info['order_status_id'] . "', 'Splited by: " . $db->escape($new_suborder_id) . $splited_msg . "', '" . $db->escape($user_details) . "',  '" . date('Y-m-d H:i:s') . "')");

            $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($new_suborder_id) . "', '" . (int) $suborder_info['order_status_id'] . "', 'Splited from: " . $db->escape($suborder_id) . $splited_msg . "', '" . $db->escape($user_details) . "',  '" . date('Y-m-d H:i:s') . "')");

            /*
             * Commented for now as it no use for now.
              //Now again change edit type = YES to for new suborder_id
              $result_query = $db->query("UPDATE `" . DB_PREFIX . "order_product` SET edit_type= 'YES' WHERE suborder_id = '" . $db->escape($new_suborder_id) . "'");
             */

            //Now Update seller_invoice table suborder_id if seller invoice is generated.
            self::_changeSellerInvoiceSellerInvoiceId($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            //Now update order_option table
            self::_updateOrderOptionForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            /*
             * Commented for now as no edit_type is updated.
              //Now update Edit history
              self::updateOrderProductEditHistoryLastModified($db, $order_id, $new_suborder_id, $order_product_id, $edit_history);
             */
            if ($is_invoice_generated) {
                $keys_for_log['new_suborder_id'] = $new_suborder_id;
                $keys_for_log['user'] = $user_details;
                self::sendMailsForSplitSuborder($controller, $keys_for_log);
            }
            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'key_name' => 'order_product_id',
                'edit_type' => 'BREAK_MOVE_SUBORDER',
                'field_name' => 'break_suborder',
                'old_value' => $suborder_id,
                'new_value' => $new_suborder_id,
                'user_id' => $edit_history['user_id'],
                'name' => $edit_history['name'],
                'user_name' => $edit_history['user_name']
            );
            foreach ($order_product_id as $value) {
                $edit_arr['key_id'] = $value;
                self::saveEditHistory($db, $edit_arr);
            }
            $db->query(" COMMIT ");
            return true;
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    public static function updateMovedSuborderProduct($controller, $order_id, $suborder_id, $order_product_id, $edit_history, $move_suborder_id) {


        $db = $controller->db;
        try {
            // Start transaction
            $db->query(" START TRANSACTION ");

            $selector = array(
                'order' => array(
                    'select' => array('order_id')
                ),
                'suborder' => array(
                    'select' => array(
                        'gst',
                        'buyer_invoice_id',
                        'invoice_no',
                        'invoice_date',
                        'invoice_prefix',
                        'order_status_id',
                        'shipping_charge',
                        'shipping_code')
                ),
                'order_product' => array()
            );
            $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
            $order_details = $order_info['order'];
            $suborder_info = $order_info['suborder'][$suborder_id];
            $products = $suborder_info['order_product'];
            unset($suborder_info['order_product']);

            $products = self::filterOrderProducts($products);

            $is_invoice_generated = false;
            if (!empty($suborder_info['invoice_no']) && $suborder_info['invoice_no'] > 0) {
                $is_invoice_generated = true;
            }
            $is_shipping_update = false;
            if (strstr($suborder_info['shipping_code'], 'weight')) {
                $is_shipping_update = true;
            }

            $new_suborder_id = $move_suborder_id;

            if ($is_shipping_update) {
                //$shipping_charge = self::_getSuborderTotalShippingForSameCity($db, $order_id, $suborder_id, $new_suborder_id);
                $readjusted_shipping = self::getReadjustedShippingForBreakSuborderIntoAnother($products, $order_product_id, (float) $suborder_info['shipping_charge']);
            }

            if ($is_invoice_generated) {
                self::_setBuyerInvoiceAsNull($db, $order_id, $suborder_id);
                //Now update in order_product table's column buyer_invoice_id as NULL by suborder_id because invoice is already generated.
                self::_resetOrderProductBuyerInvoice($db, $order_id, $suborder_id);
                self::_setAdvanceAsZeroAndUnlocked($db, $order_id, $suborder_id);
            }

            if ($is_shipping_update) {
                self::_updateShippingForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $readjusted_shipping, true);
            }

            $keys_for_log = array();
            if ($is_invoice_generated) {
                $keys_for_log = array(
                    'suborder_id' => $suborder_id,
                    'buyer_invoice_id' => $suborder_info['buyer_invoice_id'],
                    'invoice_no' => $suborder_info['invoice_no'],
                    'invoice_date' => $suborder_info['invoice_date'],
                    'invoice_prefix' => $suborder_info['invoice_prefix']
                );
            }

            //Now update in order_product table by order_product_id
            self::_changeSuborderIdForOrderProduct($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            //Now calculating suborder total according to split by order_product_id

            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($new_suborder_id) . "')");
            $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");

            //Now Update seller_invoice table suborder_id if seller invoice is generated.
            self::_changeSellerInvoiceSellerInvoiceId($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            //Now update order_option table
            self::_updateOrderOptionForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id);

            /*
             * Commented for now as no edit_type is updated.
              //Now update Edit history
              self::updateOrderProductEditHistoryLastModified($db, $order_id, $new_suborder_id, $order_product_id, $edit_history);
             */
            if ($is_invoice_generated) {
                $keys_for_log['new_suborder_id'] = $new_suborder_id;
                $keys_for_log['user'] = $user_details;
                self::sendMailsForSplitSuborder($controller, $keys_for_log);
            }
            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'key_name' => 'order_product_id',
                'edit_type' => 'BREAK_MOVE_SUBORDER',
                'field_name' => 'move_suborder',
                'old_value' => $suborder_id,
                'new_value' => $new_suborder_id,
                'user_id' => $edit_history['user_id'],
                'name' => $edit_history['name'],
                'user_name' => $edit_history['user_name']
            );
            foreach ($order_product_id as $value) {
                $edit_arr['key_id'] = $value;
                self::saveEditHistory($db, $edit_arr);
            }
            $db->query(" COMMIT ");
            return true;
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    public static function updateSplittedSuborderOrderProductsQtyWise($controller, $order_id, $suborder_id, $order_product_id, $edit_history, $qty_arr) {

        $db = $controller->db;
        try {
            // Start transaction
            $db->query(" START TRANSACTION ");

            $selector = array(
                'order' => array(
                    'select' => array('order_id')
                ),
                'suborder' => array(
                    'select' => array(
                        'gst',
                        'buyer_invoice_id',
                        'invoice_no',
                        'invoice_date',
                        'invoice_prefix',
                        'order_status_id',
                        'shipping_charge',
                        'shipping_code')
                ),
                'order_product' => array()
            );
            $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
            $order_details = $order_info['order'];
            $suborder_info = $order_info['suborder'][$suborder_id];
            $products = $suborder_info['order_product'];
            unset($suborder_info['order_product']);

            $products = self::filterOrderProducts($products);

            $is_invoice_generated = false;
            if (!empty($suborder_info['invoice_no']) && $suborder_info['invoice_no'] > 0) {
                $is_invoice_generated = true;
            }

            $is_shipping_update = false;
            if (strstr($suborder_info['shipping_code'], 'weight')) {
                $is_shipping_update = true;
            }

            if ($is_shipping_update) {
                $readjusted_shipping = self::getReadjustedShippingForPiecesSplitting($products, $order_product_id, (float) $suborder_info['shipping_charge'], $qty_arr);
            }

            $new_suborder_id = self::getNewSuborderId($db, $order_id, $suborder_id);
            self::_copySuborderForBreakSuborderIntoAnother($db, $is_invoice_generated, $suborder_id, $new_suborder_id, $suborder_info);
            self::_copySuborderHistoryForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id);
            self::_copySuborderAdvanceForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $is_invoice_generated);

            if ($is_shipping_update) {
                self::_updateShippingForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $readjusted_shipping);
            }

            self::_copyOrderProductAndOptionForBreakSuborderIntoAnother($db, $products, $order_product_id, $order_id, $suborder_id, $new_suborder_id, $qty_arr, $is_invoice_generated);

            //Now calculating suborder total according to split by order_product_id
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($new_suborder_id) . "')");
            $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");


            // Insert and update order_history in notes containing suborder_id
            //if buyer_invoice_id gets generated the also log this
            //User Details
            $user_details = !empty($edit_history['name']) ? $edit_history['name'] : '';
            $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $suborder_info['order_status_id'] . "', 'Splited by: " . $db->escape($new_suborder_id) . "', '" . $db->escape($user_details) . "',  '" . date('Y-m-d H:i:s') . "')");

            $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($new_suborder_id) . "', '" . (int) $suborder_info['order_status_id'] . "', 'Splited from: " . $db->escape($suborder_id) . "', '" . $db->escape($user_details) . "',  '" . date('Y-m-d H:i:s') . "')");

            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'key_name' => 'order_product_id',
                'edit_type' => 'BREAK_MOVE_SUBORDER',
                'field_name' => 'break_sets',
                'old_value' => $suborder_id,
                'new_value' => $new_suborder_id,
                'user_id' => $edit_history['user_id'],
                'name' => $edit_history['name'],
                'user_name' => $edit_history['user_name']
            );
            foreach ($order_product_id as $value) {
                $edit_arr['key_id'] = $value;
                self::saveEditHistory($db, $edit_arr);
            }
            $db->query(" COMMIT ");
            return true;
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    public static function updateMovedSuborderOrderProductsQtyWise($controller, $order_id, $suborder_id, $order_product_id, $edit_history, $qty_arr, $move_suborder_id) {

        $db = $controller->db;
        try {
            // Start transaction
            $db->query(" START TRANSACTION ");

            $selector = array(
                'order' => array(
                    'select' => array('order_id')
                ),
                'suborder' => array(
                    'select' => array(
                        'gst',
                        'buyer_invoice_id',
                        'invoice_no',
                        'invoice_date',
                        'invoice_prefix',
                        'order_status_id',
                        'shipping_charge',
                        'shipping_code')
                ),
                'order_product' => array()
            );
            $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
            $order_details = $order_info['order'];
            $suborder_info = $order_info['suborder'][$suborder_id];
            $products = $suborder_info['order_product'];
            unset($suborder_info['order_product']);

            $products = self::filterOrderProducts($products);

            $is_invoice_generated = false;
            if (!empty($suborder_info['invoice_no']) && $suborder_info['invoice_no'] > 0) {
                $is_invoice_generated = true;
            }

            $is_shipping_update = false;
            if (strstr($suborder_info['shipping_code'], 'weight')) {
                $is_shipping_update = true;
            }

            if ($is_shipping_update) {
                $readjusted_shipping = self::getReadjustedShippingForPiecesSplitting($products, $order_product_id, (float) $suborder_info['shipping_charge'], $qty_arr);
            }
            $new_suborder_id = $move_suborder_id;

            if ($is_shipping_update) {
                self::_updateShippingForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $readjusted_shipping, true);
            }

            self::_copyOrderProductAndOptionForBreakSuborderIntoAnother($db, $products, $order_product_id, $order_id, $suborder_id, $new_suborder_id, $qty_arr, $is_invoice_generated);

            //Now calculating suborder total according to split by order_product_id
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($new_suborder_id) . "')");
            $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");

            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'key_name' => 'order_product_id',
                'edit_type' => 'BREAK_MOVE_SUBORDER',
                'field_name' => 'move_sets',
                'old_value' => $suborder_id,
                'new_value' => $new_suborder_id,
                'user_id' => $edit_history['user_id'],
                'name' => $edit_history['name'],
                'user_name' => $edit_history['user_name']
            );
            foreach ($order_product_id as $value) {
                $edit_arr['key_id'] = $value;
                self::saveEditHistory($db, $edit_arr);
            }

            $db->query(" COMMIT ");
            return true;
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    public static function filterOrderProducts(&$products) {
        foreach ($products as $key => $product) {
            $edit_type_arr = array(
                'YES',
                'SELLER_LATER_DISPATCH',
                'SELLER_APPROVED',
                'SELLER_PARTIAL');
            if (in_array($product['edit_type'], $edit_type_arr) || (!empty($product['buyer_invoice_id']) && $product['buyer_invoice_id'] > 0)) {
                continue;
            } else {
                unset($products[$key]);
            }
        }
        $products = array_values(array_filter($products));
        return $products;
    }

    public static function getReadjustedShippingForBreakSuborderIntoAnother($products, $order_product_id, $shipping_charge) {
        $result = array();
        $suborder_shipping_weight = 0;
        $overall_suborder_shipping_weight = 0;
        foreach ($products as $product) {
            $overall_suborder_shipping_weight += ($product['weight_per_piece'] * $product['piece_in_set'] * $product['quantity']);
            if (in_array($product['order_product_id'], $order_product_id)) {
                $suborder_shipping_weight += ($product['weight_per_piece'] * $product['piece_in_set'] * $product['quantity']);
            }
        }

        $result['suborder_shipping_for_new'] = ROUND((((float) $suborder_shipping_weight / (float) $overall_suborder_shipping_weight) * (float) $shipping_charge), 2);
        $result['suborder_shipping_for_old'] = ROUND(((float) $shipping_charge - (float) $result['suborder_shipping_for_new']), 2);
        return $result;
    }

    public static function getReadjustedShippingForPiecesSplitting($products, $order_product_id, $shipping_charge, $qty_arr) {
        $result = array();
        $suborder_shipping_weight = 0;
        $overall_suborder_shipping_weight = 0;
//        $total_ship_weight = 0;
//        $remaining_ship_weight = 0;
        $new_suborder_ship_weight = 0;
        foreach ($products as $product) {
            $overall_suborder_shipping_weight += (float) ($product['weight_per_piece'] * $product['piece_in_set'] * $product['quantity']);
            if (in_array($product['order_product_id'], $order_product_id)) {
//                $total_pieces = $product['piece_in_set'] * $product['quantity'];
//                $remaining_pieces = $total_pieces - $qty_arr[$product['order_product_id']];
//                $total_ship_weight += (float) ($total_pieces * $product['weight_per_piece']);
//                $remaining_ship_weight += (float) ($remaining_pieces * $product['weight_per_piece']);
                $new_suborder_ship_weight += (float) ($qty_arr[$product['order_product_id']] * $product['piece_in_set'] * $product['weight_per_piece']);
            }
        }

//        $new_splitted_product_ship_weight = (float) ($total_ship_weight - $remaining_ship_weight);
        $result['suborder_shipping_for_new'] = ROUND((((float) $new_suborder_ship_weight / (float) $overall_suborder_shipping_weight) * (float) $shipping_charge), 2);
        $result['suborder_shipping_for_old'] = ROUND(((float) $shipping_charge - (float) $result['suborder_shipping_for_new']), 2);
        return $result;
    }

    private static function _getSuborderTotalShippingForSameCity($db, $order_id, $suborder_id, $new_suborder_id) {

        $sql = "SELECT SUM(shipping_charge) as total_shipping_charge
                FROM " . DB_PREFIX . "suborder
                WHERE order_id='" . (int) $order_id . "' AND
                      suborder_id IN ('" . $db->escape($suborder_id) . "', '" . $db->escape($new_suborder_id) . "')";
        $total_shipping_charge = (float) $db->query($sql)->row['total_shipping_charge'];
        return $total_shipping_charge;
    }

    private static function _setBuyerInvoiceAsNull($db, $order_id, $suborder_id) {
        $db->query("UPDATE `" . DB_PREFIX . "suborder` 
                    SET buyer_invoice_id = NULL,
                        invoice_no = 0,
                        invoice_date = NULL
                    WHERE order_id='" . (int) $order_id . "' AND
                          suborder_id ='" . $db->escape($suborder_id) . "' AND
                          buyer_invoice_id > 0");
    }

    private static function _setAdvanceAsZeroAndUnlocked($db, $order_id, $suborder_id) {
        $db->query("UPDATE `" . DB_PREFIX . "advance_voucher` 
                    SET value = 0,
                        locked = 0
                    WHERE order_id='" . (int) $order_id . "' AND
                          suborder_id ='" . $db->escape($suborder_id) . "' AND
                          status = 1");
    }

    public static function getTotalAmounts($db, $order_id, $suborder_id, $new_suborder_id = '') {
        $sql_new = " suborder_id ='" . $db->escape($suborder_id) . "'";
        if (!empty($new_suborder_id)) {
            $sql_new = " suborder_id IN ('" . $db->escape($suborder_id) . "', '" . $db->escape($new_suborder_id) . "')";
        }
        $query_sub = $db->query("SELECT suborder_id,
                                        shipping_charge,
                                        total
                                FROM " . DB_PREFIX . "suborder
                                WHERE order_id='" . (int) $order_id . "' AND
                                      " . $sql_new . "");
        $suborder_info = array();
        if ($query_sub->num_rows) {
            $suborder_info = $query_sub->rows;
            $suborder_info = array_combine(array_column($suborder_info, 'suborder_id'), $suborder_info);
        }
        return $suborder_info;
    }

    private static function _copySuborderForBreakSuborderIntoAnother($db, $is_invoice_generated, $suborder_id, $new_suborder_id, $suborder_info) {
        $key_in = array('suborder_id' => $suborder_id);
        $key_out = array(
            array(
                'suborder_id' => $new_suborder_id
            )
        );

        if ($is_invoice_generated) {
            $key_in = array(
                'suborder_id' => $suborder_id,
                'buyer_invoice_id' => $suborder_info['buyer_invoice_id'],
                'invoice_no' => $suborder_info['invoice_no'],
                'invoice_date' => $suborder_info['invoice_date'],
                'invoice_prefix' => $suborder_info['invoice_prefix']
            );
            $keys_for_log = $key_in;

            $key_out = array(
                array(
                    'suborder_id' => $suborder_id,
                    'buyer_invoice_id' => NULL,
                    'invoice_no' => 0,
                    'invoice_date' => NULL,
                    'invoice_prefix' => '',
                ),
                array(
                    'suborder_id' => $new_suborder_id,
                    'buyer_invoice_id' => NULL,
                    'invoice_no' => 0,
                    'invoice_date' => NULL,
                    'invoice_prefix' => '',
                )
            );
        }

        // Copy suborder row to another for new suborder_id 
        $db->copyRow(DB_PREFIX . 'suborder', $key_in, $key_out, $is_invoice_generated);
    }

    private static function _copySuborderHistoryForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id) {
        // Copy suborder rows to another for new suborder_id 
        //Before copy in order_history find primary or unique key
        $order_history_query = $db->query("SELECT order_history_id FROM " . DB_PREFIX . "order_history where order_id='" . (int) $order_id . "' and suborder_id='" . $db->escape($suborder_id) . "'");
        $order_history_id = $order_history_query->rows;
        foreach ($order_history_id as $key => $value) {
            $key_in = array();
            $key_in['suborder_id'] = $suborder_id;
            $key_in['order_history_id'] = $value['order_history_id'];
            $key_out = array(
                array(
                    'suborder_id' => $new_suborder_id,
                    'order_history_id' => $value['order_history_id']
                )
            );
            $db->copyRow(DB_PREFIX . 'order_history', $key_in, $key_out, false, array('order_history_id'));
        }
    }

    private static function _copySuborderAdvanceForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $is_invoice_generated) {
        //Before copy in advance_voucher find primary or unique key
        $advance_voucher_query = $db->query("SELECT advance_voucher_id, advance_voucher_no, locked, value FROM " . DB_PREFIX . "advance_voucher where order_id='" . (int) $order_id . "' and suborder_id='" . $db->escape($suborder_id) . "'");
        $advance_voucher_id = $advance_voucher_query->rows;
        $max_advance_voucher_no = (int) ($db->query("SELECT MAX(advance_voucher_no) as max_advance_voucher_no FROM " . DB_PREFIX . "advance_voucher")->row['max_advance_voucher_no']) + 1;
        //$max_advance_voucher_no_increment = $max_advance_voucher_no;
        foreach ($advance_voucher_id as $key => $value) {
            $key_in = array();
            $key_in['suborder_id'] = $suborder_id;
            $key_in['advance_voucher_id'] = $value['advance_voucher_id'];
            $key_in['advance_voucher_no'] = $value['advance_voucher_no'];
            $key_in['value'] = $value['value'];
            $key_in['locked'] = $value['locked'];
            if ($is_invoice_generated) {
                $key_out = array(
                    array(
                        'suborder_id' => $suborder_id,
                        'advance_voucher_id' => $value['advance_voucher_id'],
                        'advance_voucher_no' => $value['advance_voucher_no'],
                        'value' => 0,
                        'locked' => 0,
                    ),
                    array(
                        'suborder_id' => $new_suborder_id,
                        'advance_voucher_id' => $value['advance_voucher_id'],
                        'advance_voucher_no' => $max_advance_voucher_no,
                        'value' => 0,
                        'locked' => 0,
                    )
                );
            } else {
                $key_out = array(
                    array(
                        'suborder_id' => $new_suborder_id,
                        'advance_voucher_id' => $value['advance_voucher_id'],
                        'advance_voucher_no' => $max_advance_voucher_no,
                        'value' => 0,
                        'locked' => 0,
                    )
                );
            }



            $db->copyRow(DB_PREFIX . 'advance_voucher', $key_in, $key_out, $is_invoice_generated, array('advance_voucher_id'));
            $max_advance_voucher_no++;
        }
    }

    private static function _copyOrderProductAndOptionForBreakSuborderIntoAnother($db, $products, $order_product_id, $order_id, $suborder_id, $new_suborder_id, $qty_arr, $is_invoice_generated) {

        //Now get order_option if exists for order_product_id
        $sql = "SELECT order_option_id,
                           order_product_id
                    FROM " . DB_PREFIX . "order_option
                    WHERE order_id ='" . (int) $order_id . "' AND
                          suborder_id = '" . $db->escape($suborder_id) . "' AND
                          order_product_id IN (" . implode(',', $order_product_id) . ")";
        $option_qry = $db->query($sql);
        $option_order_products = array();
        if ($option_qry->num_rows) {
            $option_order_products_qry = $option_qry->rows;
            foreach ($option_order_products_qry as $value) {
                $option_order_products[$value['order_product_id']] = $value['order_option_id'];
            }
        }

        //Now update in order_product table by order_product_id
        $sellected_order_products = array_combine(array_column($products, 'order_product_id'), $products);

        foreach ($order_product_id as $value) {
//            $total_pieces = $sellected_order_products[$value]['quantity'] * $sellected_order_products[$value]['piece_in_set'];
//            $remaining_pieces = $total_pieces - $qty_arr[$value];
            $total_sets = $sellected_order_products[$value]['quantity'];
            $remaining_sets = $total_sets - $qty_arr[$value];
            $key_in = array(
                'order_product_id' => $value,
                'suborder_id' => $suborder_id,
                'quantity' => $total_sets
            );

            $key_out = array(
                array(
                    'order_product_id' => $value,
                    'suborder_id' => $suborder_id,
                    'quantity' => $remaining_sets
                ),
                array(
                    'order_product_id' => $value,
                    'suborder_id' => $new_suborder_id,
                    'quantity' => $qty_arr[$value]
                )
            );


            // Copy suborder row to another for new suborder_id 
            $last_id = $db->copyRow(DB_PREFIX . 'order_product', $key_in, $key_out, true, array('order_product_id'));


            if (!empty($option_order_products[$value]) && !empty($last_id)) {
                $key_in_option = array(
                    'order_option_id' => $option_order_products[$value],
                    'order_product_id' => $value,
                    'suborder_id' => $suborder_id
                );
                $key_out_option = array(
                    array(
                        'order_option_id' => $option_order_products[$value],
                        'order_product_id' => $last_id,
                        'suborder_id' => $new_suborder_id
                    )
                );
                $db->copyRow(DB_PREFIX . 'order_option', $key_in_option, $key_out_option, false, array('order_option_id'));
            }
        }
    }

    private static function _updateShippingForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $readjusted_shipping, $is_moved_suborder = false) {
        $suborder_update_query_old = $db->query("UPDATE `" . DB_PREFIX . "suborder` SET  shipping_charge = '" . (float) $readjusted_shipping['suborder_shipping_for_old'] . "' WHERE order_id='" . (int) $order_id . "' and suborder_id ='" . $db->escape($suborder_id) . "'");

        $shipping_char = "shipping_charge = '" . (float) $readjusted_shipping['suborder_shipping_for_new'] . "'";
        if ($is_moved_suborder) {
            $shipping_char = "shipping_charge = shipping_charge + " . (float) $readjusted_shipping['suborder_shipping_for_new'];
        }

        $suborder_update_query_new = $db->query("UPDATE `" . DB_PREFIX . "suborder` 
                SET  " . $shipping_char . "
                WHERE order_id='" . (int) $order_id . "' AND 
                      suborder_id ='" . $db->escape($new_suborder_id) . "'");
    }

    public static function getSubordersFromSameCity($db, $order_id, $suborder_id) {
        $final_suborder_arr = array();
        $suborder_id_search = $suborder_id;
        $suborder_id_exp = explode('-', $suborder_id);
        if (!empty($suborder_id_exp[0]) && !empty($suborder_id_exp[1])) {
            $suborder_id_search = $suborder_id_exp[0] . '-' . $suborder_id_exp[1];
        }


        $sql = "SELECT suborder_id,
                       buyer_invoice_id,
                       order_status_id
                FROM " . DB_PREFIX . "suborder
                WHERE order_id='" . (int) $order_id . "' AND
                      suborder_id LIKE '%" . $db->escape($suborder_id_search) . "%'";
        $suborder_id_query = $db->query($sql);

        if ($suborder_id_query->num_rows) {
            $suborder_id_arr = $suborder_id_query->rows;

            if (count($suborder_id_arr) > 1) {

                foreach ($suborder_id_arr as $value) {
                    if (($value['buyer_invoice_id'] == 0 || $value['buyer_invoice_id'] == NULL) && ($value['suborder_id'] != $suborder_id) && ($value['order_status_id'] == 1 || $value['order_status_id'] == 9 || $value['order_status_id'] == 16)) {
                        $final_suborder_arr[] = $value['suborder_id'];
                    }
                }
                if (!empty($final_suborder_arr)) {
                    $final_suborder_arr = array_unique($final_suborder_arr);
                }
            }
        }
        return $final_suborder_arr;
    }

    public static function cancelSuborderBuyerInvoice($controller, $order_id, $suborder_id) {
        $db = $controller->db;
        try {

            // Start transaction
            $db->query(" START TRANSACTION ");
            $selector = array(
                'order' => array(
                    'select' => array('order_id')
                ),
                'suborder' => array(
                    'select' => array(
                        'gst',
                        'buyer_invoice_id',
                        'invoice_no',
                        'invoice_date',
                        'invoice_prefix',
                        'order_status_id')
                )
            );

            $order_info = OrderInfo::getOrderInfo($db, $order_id, $suborder_id, $selector);
            $suborder_info = $order_info['suborder'][$suborder_id];

            $is_cancel_buyer_invoice = false;
            if ($suborder_info['order_status_id'] == 1 || $suborder_info['order_status_id'] == 9 || $suborder_info['order_status_id'] == 16) {
                $is_cancel_buyer_invoice = true;
            }
            if (!$is_cancel_buyer_invoice) {
                $db->query(" ROLLBACK ");
                return false;
            }

            self::_setBuyerInvoiceAsNull($db, $order_id, $suborder_id);
            self::_resetOrderProductBuyerInvoice($db, $order_id, $suborder_id);
            self::_updateAdvanceVoucherForCancelSuborder($db, $order_id, $suborder_id);

            $log = array(
                'suborder_id' => $suborder_id,
                'invoice_no' => $suborder_info['invoice_no'],
                'invoice_date' => $suborder_info['invoice_date'],
                'invoice_prefix' => $suborder_info['invoice_prefix'],
                'user' => $user_details
            );
            $db->query(" COMMIT ");
            self::sendMailsForSplitSuborder($controller, $log, true);
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    private static function _updateOrderOptionForBreakSuborderIntoAnother($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id) {

        $db->query("UPDATE " . DB_PREFIX . "order_option SET suborder_id= '" . $db->escape($new_suborder_id) . "' WHERE order_product_id in (" . implode(',', $order_product_id) . ") AND suborder_id = '" . $db->escape($suborder_id) . "' AND order_id = '" . (int) $order_id . "'");
    }

    private static function _changeSellerInvoiceSellerInvoiceId($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id) {

        $seller_invoice_query = $db->query("SELECT DISTINCT (seller_invoice_id) FROM " . DB_PREFIX . "order_product WHERE order_product_id IN (" . implode(',', $order_product_id) . ")");
        $seller_invoice_ids = '';
        if ($seller_invoice_query->num_rows) {
            $seller_invoice_id_arr = $seller_invoice_query->rows;
            foreach ($seller_invoice_id_arr as $seller_invoice_id) {
                if ($seller_invoice_id['seller_invoice_id'] > 0 && $seller_invoice_id['seller_invoice_id'] != NULL)
                    $seller_invoice_ids .= $seller_invoice_id['seller_invoice_id'] . ',';
            }
            if ($seller_invoice_ids != '' && $seller_invoice_ids != ',') {
                $seller_invoice_ids = rtrim($seller_invoice_ids, ',');
                $result_query = $db->query("UPDATE " . DB_PREFIX . "seller_invoice SET suborder_id= '" . $db->escape($new_suborder_id) . "' WHERE seller_invoice_id IN ($seller_invoice_ids) AND order_id = '" . (int) $order_id . "' AND suborder_id = '" . $db->escape($suborder_id) . "'");
            }
        }
    }

    private static function _changeSuborderIdForOrderProduct($db, $order_id, $suborder_id, $new_suborder_id, $order_product_id) {
        $db->query("UPDATE `" . DB_PREFIX . "order_product` 
                    SET suborder_id = '" . $db->escape($new_suborder_id) . "' 
                    WHERE order_id ='" . (int) $order_id . "' AND
                          suborder_id ='" . $db->escape($suborder_id) . "' AND
                          order_product_id in (" . implode(',', $order_product_id) . ")");
    }

    private static function _resetOrderProductBuyerInvoice($db, $order_id, $suborder_id) {
        $sql = "UPDATE " . DB_PREFIX . "order_product SET
                    buyer_invoice_id = NULL
                WHERE buyer_invoice_id > 0 AND
                      order_id ='" . (int) $order_id . "' AND
                      suborder_id ='" . $db->escape($suborder_id) . "'";
        $db->query($sql);
    }

    private static function _updateAdvanceVoucherForCancelSuborder($db, $order_id, $suborder_id) {
        $sql = "UPDATE " . DB_PREFIX . "advance_voucher SET
                    locked = 0,
                    value = 0
                WHERE order_id ='" . (int) $order_id . "' AND
                      suborder_id ='" . $db->escape($suborder_id) . "'";
        $db->query($sql);
    }

    public static function getNewSuborderId($db, $order_id, $suborder_id) {
        $suborder_exp_arr = explode('-', $suborder_id);
        if (count($suborder_exp_arr) > 2) {
            $suborder_id = $suborder_exp_arr[0] . '-' . $suborder_exp_arr[1];
        }
        $new_suborder_id = '';
        $sql = "SELECT suborder_id FROM " . DB_PREFIX . "suborder WHERE order_id = '" . (int) $order_id . "' and suborder_id LIKE '%" . $db->escape($suborder_id) . "%'";
        $result = $db->query($sql);
        $x = 0;
        $suborder_num_arr = array();
        foreach ($result->rows as $suborder) {
            $suborder_exp = explode('-', $suborder['suborder_id']);
            if (count($suborder_exp) > 2) {
                $suborder_num_arr[$x] = $suborder_exp[2];
            } else {
                $suborder_num_arr[$x] = 0;
            }
            $x++;
        }

        $suborder_number = max($suborder_num_arr);
        if ($suborder_number == 0) {
            $new_suborder_id = $suborder_id . '-' . 2;
        } else {
            $new_suborder_id = $suborder_exp_arr[0] . '-' . $suborder_exp_arr[1] . '-' . ++$suborder_number;
        }
        return $new_suborder_id;
    }

    public static function updateOrderProductEditHistoryLastModified($db, $order_id, $suborder_id, $order_product_id, $edit_history) {
        $sql = "SELECT oop.order_id,
                       oop.suborder_id,
                       oop.order_product_id,
                       oop.edit_type, 
                       oop.edit_history 
                FROM oc_order_product oop 
                WHERE order_id='" . (int) $order_id . "' AND
                      suborder_id='" . $db->escape($suborder_id) . "' AND
                      order_product_id in (" . implode(',', $order_product_id) . ")";
        $result = $db->query($sql)->rows;

        $previous_edit_history = array();
        foreach ($result as $key => $value) {
            $previous_edit_history = unserialize($value['edit_history']);
            $edit_history['comment'] = $value['edit_type'];
            array_push($previous_edit_history, $edit_history);

            $db->query("UPDATE `" . DB_PREFIX . "order_product` SET edit_history= '" . $db->escape(serialize($previous_edit_history)) . "', last_modified = NOW() WHERE order_id = '" . (int) $order_id . "' AND suborder_id = '" . $db->escape($suborder_id) . "' AND order_product_id='" . (int) $value['order_product_id'] . "'");
        }
        return true;
    }

    /**
     * Sending mails
     * @param $data array of mail details
     */
    public static function sendMailsForSplitSuborder($controller, $data, $only_cancel_invoice = false) {

        if (method_exists($controller, 'get')) {
            $config = $controller->get('config');
        } else {
            $config = $controller->config;
        }
        if ($only_cancel_invoice) {
            $html = 'Due to some reasons Suborder no ' . $data['suborder_id'] . ' buyer invoice  is canceled after its invoice being generated already, Invoice no ' . $data['invoice_prefix'] . $data['invoice_no'] . ', dated ' . $data['invoice_date'] . ', ref: ' . $data['buyer_invoice_id'] . ', has been marked cancelled. <br><br>
New invoice(s) will be generated for the suborder no ' . $data['suborder_id'] . ' <br><br>';
        } else {
            $html = MailTemplate::splitSuborderMailTemplate($data);
        }

        $subject = 'Invoice ' . $data['invoice_prefix'] . $data['invoice_no'] . ' for Suborder No. ' . $data['suborder_id'] . ' is canceled.';
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

    /**
     * Sending mails
     * @param $data array of mail details
     */
    public static function sendMailsForAddItemInSuborder($controller, $order_id, $suborder_id, $data, $order_product_data) {

        if (method_exists($controller, 'get')) {
            $config = $controller->get('config');
        } else {
            $config = $controller->config;
        }

        $controller->load->model('sale/order');
        $seller_id_arr = array_unique(array_column($order_product_data, 'seller_id'));
        if (!empty($data['order_status_id']) && ($data['order_status_id'] == 9 || $data['order_status_id'] == 16)) {
            foreach ($seller_id_arr as $key => $seller_id) {
                $controller->model_sale_order->sendSellerMail($order_id, $suborder_id, $seller_id);
            }
        }

        $order_no = $suborder_id;
        $suborder_exp_arr = explode('-', $suborder_id);
        if (!empty($suborder_exp_arr[0])) {
            $order_no = $suborder_exp_arr[0];
        }
        $data['order_no'] = $order_no;

        $html = MailTemplate::sendMailsForAddItemInSuborderToCustomer($data);

        $subject = 'There has been update for your Order No. ' . $order_no . '.';
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['info']['email_id'], EMAIL_IDS['info']['name']);
        $mail->addAddress($data['email']);
        $mail->addCC(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
    }

    /**
     * Method for delete Payment Entry In Edit Order Payment
     * @param : payment_id : Integer of payment id,
     * @param : data : array of data i.e.{payment_id, order_id, selected_value, action_comment}
     * @return NULL
     * @author : vikas, Feb 2018
     */
    public static function deletePaymentEntryInEditOrderPayment($controller, $data = array()) {
        $sql = "SELECT  order_no,
                        txn_status,
                        payment_mode,
                        amount,
                        txn_date_time,
                        reference,
                        txn_status,
                        successfull,
                        edit_history, 
                        payment_gateway 
                FROM " . DB_PREFIX . "order_payment
                WHERE payment_id = " . (int) $data['payment_id'];
        $query = $controller->db->query($sql);

        if ($query->num_rows) {

            $edit_history = array();
            $edit_history = unserialize($query->row['edit_history']);
            $user       = array();
            $user_group = '';
            $user_id    = 0;
            if(!empty($controller->user)){
                $user_id    = $controller->user->getId();
                $user       = $controller->user->getUserName($controller->user->getId());
                $user_group = $controller->user->getGroupName();
            }
            $user_id     = $user_id;
            $name        = $user['name'] ?? $data['name'] ?? '';
            $username    = $user['username'] ?? $data['username'] ?? '';
            $user_type   = $controller->db->escape($user_group);
            $server      = $_SERVER['HTTP_USER_AGENT'];
            $request_obj = new Request();
            $ip = $request_obj->getIpAddress;
            $date = date('Y-m-d H:i:s');
            $reference = $username . ' initiated [' . $data['selected_value'] . '] ' . ' ' . $query->row['reference'];

            $edit_history[] = array(
                                'user_id'    => $user_id,
                                'name'       => $name,
                                'user_name'  => $username,
                                'user_type'  => $user_type,
                                'user_ip'    => $ip,
                                'user_agent' => $server,
                                'date_added' => $date,
                                'comment'    => $data['action_comment']
                                );

            $records_logs_map = array(
                'txn_status' => array(
                    'old_value' => $query->row['txn_status'],
                    'new_value' => $data['selected_value'],
                ),
                'successfull' => array(
                    'old_value' => $query->row['successfull'],
                    'new_value' => '0',
                ),
                'edit_history' => array(
                    'old_value' => $query->row['edit_history'],
                    'new_value' => serialize($edit_history),
                ),
                'reference' => array(
                    'old_value' => $query->row['reference'],
                    'new_value' => $reference,
                ),
            );

            foreach ($query->row as $key => $value) {
                if (array_key_exists($key, $records_logs_map)) {
                    $records_logs = array(
                        'order_id' => $data['order_id'],
                        'suborder_id' => '',
                        'key_id' => $data['payment_id'],
                        'key_name' => 'order_payment_id',
                        'edit_type' => 'ORDER_PAYMENT',
                        'field_name' => $key,
                        'old_value' => $records_logs_map[$key]['old_value'],
                        'new_value' => $records_logs_map[$key]['new_value'],
                        'user_id' => $user_id,
                        'comment' => '',
                        'name' => $data['name'],
                        'user_name' => $data['user_name']
                    );

                    self::saveEditHistory($controller->db, $records_logs);
                }
            }

            $sql = "UPDATE " . DB_PREFIX . "order_payment
                    SET txn_status   = '" . $controller->db->escape($data['selected_value']) . "',
                        successfull  = 0,
                        reference    =  '" . $controller->db->escape($reference) . "',
                        edit_history =  '" . $controller->db->escape(serialize($edit_history)) . "'
                    WHERE payment_id =  " . (int) $data['payment_id'];
            $controller->db->query($sql);
            
            // Change status to 0 for rows in advance_voucher table, against payment_id
            $controller->db->query("UPDATE " . DB_PREFIX . "advance_voucher 
                                    SET status = 0 
                                    WHERE payment_id = " . (int) $data['payment_id']);
            
            // Mail only if the payment_gateway is not wsb_credit
            if ( $query->row['payment_gateway'] !== 'wsb_credit' ) {

                $data_mail = array(
                    'order_no'        => $query->row['order_no'],
                    'payment_gateway' => $query->row['payment_gateway'],
                    'txn_status'      => $data['selected_value'],
                    'payment_mode'    => $query->row['payment_mode'],
                    'amount'          => $query->row['amount'],
                    'txn_date_time'   => $query->row['txn_date_time'],
                    'reference'       => $reference,
                    'action_comment'  => $data['action_comment'],
                    'mail_subject'    => 'Cancelled'
                );            

                self::mailForPaymentCancelledToAdmin($controller, $data_mail);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Method for Mail for payment cancelled to admin
     * @param : $controller : object of controller
     * @param : data_mail : array of data i.e.{order_no, payment_gateway etc...}
     * @param : mail_subject : String of mail subject
     * @return NULL
     * @author : vikas, Feb 2018
     */
    private static function mailForPaymentCancelledToAdmin($controller, $data_mail = array()) {

        $html = MailTemplate::mailForPaymentCancelledToAdmin($data_mail);
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $controller->config->get('config_mail_smtp_hostname');
        $mail->Port = $controller->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $controller->config->get('config_mail_smtp_username');
        $mail->Password = $controller->config->get('config_mail_smtp_password');
        $mail->setFrom('info@wholesalebox.in', 'WholesaleBox Info');
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
        $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->Subject = 'Payment ' . $data_mail['mail_subject'] . ' - ' . $data_mail['order_no'];
        $mail->msgHTML($html);
        $mail->send();
    }

    public static function getProductLists($data) {
        //$url = HTTPS_SERVER . 'api/category/product_list&page=1&order=ASC&search=' . $search . '&stock_filter=0&clearance_sale=0&last_filter_action=false';
        $url = HTTPS_CATALOG . 'api/category/product_list';
        if (!empty($data['search'])) {
            $url .= '&search=' . trim($data['search']);
        } else {
            $url .= '&search=';
        }
        if (!empty($data['path'])) {
            $url .= '&path=' . $data['path'];
        } else {
            $url .= '&path=';
        }
        if (!empty($data['filter'])) {
            $url .= '&filter=' . $data['filter'];
        } else {
            $url .= '&filter=';
        }
        if (!empty($data['page'])) {
            $url .= '&page=' . $data['page'];
        } else {
            $url .= '&page=1';
        }
        if (!empty($data['sort_order'])) {
            $url .= '&order=' . $data['sort_order'];
        } else {
            $url .= '&order=ASC';
        }
        if (!empty($data['stock_filter'])) {
            $url .= '&stock_filter=' . $data['stock_filter'];
        } else {
            $url .= '&stock_filter=0';
        }
        if (!empty($data['clearance_sale'])) {
            $url .= '&clearance_sale=' . $data['clearance_sale'];
        } else {
            $url .= '&clearance_sale=0';
        }
        if (!empty($data['last_filter_action'])) {
            $url .= '&last_filter_action=' . $data['last_filter_action'];
        } else {
            $url .= '&last_filter_action=false';
        }

        if (!empty($data['handpicked_ids'])) {
            $url .= '&handpicked_ids=' . $data['handpicked_ids'];
        }
        $url .= '&limit=5';
        $url .= '&search_all_products=1';

//         $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=Aakara%20Gold%20Vol%201%20Kurti%20Catalog&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//         $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=P88_JP_PD_Plane_Semi_Patiyala&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//         $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=169_ST_ETHNIC1&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//         $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=034ST7010A092&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//         $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=228DL796BBABF&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//        $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=223_ST_ORANGE-T&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//        $ch = curl_init('https://www.wholesalebox.in/api/category/product_list&search=' . trim($data['search']) . '&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5');
//        echo 'https://www.wholesalebox.in/api/category/product_list&search=' . trim($data['search']) . '&page=&page=1&order=ASC&stock_filter=0&clearance_sale=0&last_filter_action=false&path=&filter=&limit=5';
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function getProductDetailsFromApi(int $product_id) {
        $url = HTTPS_CATALOG . 'api/product/getProductDetails/' . (int) $product_id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function getSellerPickupCityCode($db, $seller_ids) {
        $seller = array();
        $sql = "SELECT seller_id,
                       pickup_city_code 
                FROM oc_ms_seller
                WHERE seller_id IN (" . $seller_ids . ")";
        $seller_query = $db->query($sql);
        if ($seller_query->num_rows) {
            foreach ($seller_query->rows as $value) {
                $seller_id = $value['seller_id'];
                $seller[$seller_id] = $value['pickup_city_code'];
            }
        }
        return $seller;
    }

    public static function addOrderProductItem($controller, $order_id, $suborder_id, $data) {
        $db = $controller->db;
        $shipping_code = !empty($data['shipping_code']) ? $data['shipping_code'] : '';
        $store_pickup = 0;
        if ($data['shipping_code'] == 'weight.weight_0' || $data['shipping_code'] == 'store_pickup') {
            $store_pickup = 1;
        }
        try {
            // Start transaction
            $db->query(" START TRANSACTION ");


            if (!empty($data['products'])) {
                $order_product_data = array();
                foreach ($data['products'] as &$product) {
                    if ($product['new_qty'] == 0)
                        continue;
                    
                    /*SOR product checking*/
                        $sor_sql = "SELECT sor_days, sor_type FROM " . DB_PREFIX . "product_sor_terms
                            WHERE product_id = '" . (int)$product['product_id'] . "' ";
                        $sor_query = $db->query($sor_sql);

                        if($sor_query->num_rows && !empty($sor_query->row['sor_days']))
                        {
                          if($product['piece_in_set'] > 1)
                          {
                            $product['name'] = 'SOR-'.$sor_query->row['sor_days'].'Days-FullSetReturnOnly-'.$product['name'];
                          }
                          else
                          {
                           $product['name'] = 'SOR-'.$sor_query->row['sor_days'].'Days-'.$product['name'];  
                          }
                        }

                    $quantity = $product['new_qty'];
                    $query = "INSERT INTO " . DB_PREFIX . "order_product
                              SET order_id = '" . (int) $order_id . "',
                                  suborder_id = '" . $db->escape($suborder_id) . "',
                                  product_id = '" . (int) $product['product_id'] . "',
                                  name = '" . $db->escape($product['name']) . "',
                                  model = '" . $db->escape($product['model']) . "', 
                                  hsn_code = '" . $db->escape($product['hsn_code']) . "', 
                                  quantity = '" . (int) $quantity . "',
                                  piece_in_set = '" . (int) $product['piece_in_set'] . "',
                                  price_per_piece = '" . (float) $product['selling_price'] . "',
                                  discount_per_piece = '" . (float) $product['discount_per_piece'] . "',
                                  discount_breakup = '" . $db->escape($product['discount_breakup']) . "',
                                  weight_per_piece = '" . (float) $product['weight'] . "',
                                  output_tax_rates = '" . $db->escape($product['tax_rate']) . "',
                                  comment = '" . $db->escape($product['set_description']) . "', 
                                  store_sales = '" . $db->escape($product['store_sales']) . "', 
                                  seller_id = '" . (int) $product['seller_id'] . "', 
                                  seller_sku = '" . $db->escape($product['sku']) . "',
                                  transfer_price_per_piece = '" . (float) $product['price'] . "',
                                  seller_input_tax = '" . (float) $product['seller_tax'] . "',
                                  store_pickup = '" . $store_pickup . "',
                                  seller_cst = '0',
                                  unit_id = '" . (int) $product['unit_id'] . "', 
                                  customer_comment = '',
                                  sor_product = '" . (int) $product['sor_product'] . "',
                                  wsb_purchase_id = '" . (int) $product['wsb_purchase_id'] . "',
                                  franchise_id = '" . (int) $product['franchise_id'] . "',
                                  notes = '',
                                  is_returnable = '" . (int) (!$product['non_returnable']) . "',
                                  combo_product_id = '" . (int) $product['combo_product_id'] . "'";

                    $db->query($query);

                    $order_product_id = $db->getLastId();

                    //for sor terms insert
                    if($order_product_id && !empty($sor_query->row['sor_days']))
                    {
                      $db->query("INSERT INTO " . DB_PREFIX . "order_product_sor_terms
                                          SET order_product_id = '" . (int) $order_product_id . "',
                                              sor_type = '" . $db->escape($sor_query->row['sor_type']) . "',
                                              limit_days = '" . (int) $sor_query->row['sor_days'] . "'");
                    }  


                    $order_product_data[] = array(
                        'order_product_id' => $order_product_id,
                        'quantity' => $quantity,
                        'product_id' => $product['product_id'],
                        'seller_id' => $product['seller_id'],
                        'product_code' => $product['model']
                    );

                    if (!empty($product['options'])) {
                        $option = $product['options'];

                        // Order Options are inserted to ensure proper inventory reduction takes place
                        // We update comment also, and use that to identify the product level comments.
                        // Order Options (unlike default Opencart are not used for displaying in backend etc.
                        $db->query("INSERT INTO " . DB_PREFIX . "order_option
                                    SET order_id = '" . (int) $order_id . "',
                                        suborder_id = '" . $db->escape($suborder_id) . "',
                                        order_product_id = '" . (int) $order_product_id . "',
                                        product_option_id = '" . (int) $option['product_option_id'] . "',
                                        product_option_value_id = '" . (int) $option['product_option_value_id'] . "',
                                        name = '" . $db->escape($option['tab_name']) . "',
                                        value = '" . $db->escape($option['name']) . "',
                                        type = '" . $db->escape($option['type']) . "'");

                        $option_comment = (int) $product['piece_in_set'] > 1 ? 'Set of ' : 'Single piece of ';
                        $option_comment .= $option['tab_name'] . ": " . $option['name'] . ". " . $product['customer_comment'];
                        $db->query("UPDATE " . DB_PREFIX . "order_product
                                    SET comment = '" . $db->escape($option_comment) . "'
                                    WHERE order_product_id = '" . (int) $order_product_id . "'");
                    }
                    $product['store_pickup'] = $store_pickup;
                }
                if ($data['stock_transfer'] == 1) {
                    if (!empty($order_product_data)) {
                        $order_product_id_arr = array_column($order_product_data, 'order_product_id');
                        $stock_tran_data = array(
                            'order_product_id' => $stock_tran_data,
                            'user_id' => $data['user_id'],
                            'user_name' => $data['user_name'],
                            'name' => $data['user_name']
                        );
                        self::updateToStockTransfer($db, $order_id, $stock_tran_data);
                    }
                } else {
                    if ($data['discount_type'] == 1) {
                        self::refreshOrCustomPaychargeDiscount($controller, $order_id, '', $data);
                    }
                }
                self::_updateStockAfterAddItemInOrder($db, $order_product_data);
            }


            if ($store_pickup == 0 && strstr($shipping_code, 'weight')) {
                $shipping_details = json_decode(OrderEdit::getShippingDetails($db, $order_id, $suborder_id), true);
                $shipping_charge_arr = array();
                if (!empty($shipping_details['shipping_method']['weight']['quote'])) {
                    $shipping_charge_arr = array_combine(array_column($shipping_details['shipping_method']['weight']['quote'], 'code'), $shipping_details['shipping_method']['weight']['quote']);
                }

                self::changeShippingCharge($db, $shipping_charge_arr[$shipping_code]['cost'], $order_id, $suborder_id);
            }
            self::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id, true);
            if (!empty($order_product_data)) {
                $product_name_qty = '';
                foreach ($order_product_data as $key => $value) {
                    $records_logs = array(
                        'order_id' => $order_id,
                        'suborder_id' => $suborder_id,
                        'key_id' => $value['order_product_id'],
                        'key_name' => 'order_product_id',
                        'edit_type' => 'ADD_PRODUCT',
                        'field_name' => 'new entry',
                        'old_value' => 0,
                        'new_value' => "quantity: " . $value['quantity'],
                        'user_id' => $data['user_id'],
                        'comment' => '',
                        'name' => $data['name'],
                        'user_name' => $data['user_name']
                    );

                    self::saveEditHistory($db, $records_logs);
                    $product_name_qty .= $value['product_code'] . " :- Sets: " . $value['quantity'] . "<br>";
                }
                $msg = 'Few new products has been added to this suborder: <br>' . $product_name_qty;
                $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $data['order_status_id'] . "', '" . $db->escape($msg) . "', '" . $db->escape($data['name']) . "',  '" . date('Y-m-d H:i:s') . "')");
            }
            $db->query(" COMMIT ");
            self::sendMailsForAddItemInSuborder($controller, $order_id, $suborder_id, $data, $order_product_data);
            return true;
        } catch (Exception $e) {
            $db->query(" ROLLBACK ");
            echo $e->getMessage();
            return false;
        }
    }

    public static function updateOrderProductForSuborder($controller, $order_id, $suborder_id, $for_update_suborder_products, $data_arr) {
        $db = $controller->db;
        $order_product_data = array();
        foreach ($data_arr['product_info'] as $order_product_id => $order_product_info) {
            $product_id = $order_product_info['product_id'];
            if (!empty($for_update_suborder_products[$product_id]) && empty($order_product_info['seller_invoice_id'] && ($order_product_info['edit_type'] == 'YES' || $order_product_info['edit_type'] == 'SELLER_LATER_DISPATCH'))) {
                if (!empty($for_update_suborder_products[$product_id]['product_details'])) {
                    foreach ($for_update_suborder_products[$product_id]['product_details'] as $value) {
                        if ($data_arr['product_option_info'][$order_product_id]['product_option_id'] == $value['product_option_id'] && $data_arr['product_option_info'][$order_product_id]['product_option_value_id'] == $value['product_option_value_id']) {

                            $sql = "UPDATE oc_order_product 
                                    SET quantity=quantity + " . (int) $value['qty'] . "
                                    WHERE order_product_id='" . (int) $order_product_id . "' AND
                                          order_id ='" . (int) $order_id . "' AND
                                          suborder_id ='" . $db->escape($suborder_id) . "' AND
                                          product_id ='" . (int) $product_id . "' AND
                                          (seller_invoice_id = 0 OR seller_invoice_id IS NULL) AND
                                          edit_type IN ('YES', 'SELLER_LATER_DISPATCH')";
                            if ($db->query($sql)) {
                                $order_product_data[] = array(
                                    'order_product_id' => $order_product_id,
                                    'quantity' => (int) $value['qty'],
                                    'old_quantity' => (int) $order_product_info['quantity'],
                                    'product_id' => $product_id,
                                    'seller_id' => $order_product_info['seller_id'],
                                    'product_code' => $order_product_info['model']
                                );
                            }
                        }
                    }
                } else {
                    if (!empty($for_update_suborder_products[$product_id]['qty'])) {

                        $sql = "UPDATE oc_order_product 
                                SET quantity=quantity + " . (int) $for_update_suborder_products[$product_id]['qty'] . "
                                WHERE order_product_id='" . (int) $order_product_id . "' AND
                                      order_id ='" . (int) $order_id . "' AND
                                      suborder_id ='" . $db->escape($suborder_id) . "' AND
                                      product_id ='" . (int) $product_id . "' AND
                                      (seller_invoice_id = 0 OR seller_invoice_id IS NULL) AND
                                      edit_type IN ('YES', 'SELLER_LATER_DISPATCH')";
                        if ($db->query($sql)) {
                            $order_product_data[] = array(
                                'order_product_id' => $order_product_id,
                                'quantity' => (int) $for_update_suborder_products[$product_id]['qty'],
                                'old_quantity' => (int) $order_product_info['quantity'],
                                'product_id' => $product_id,
                                'seller_id' => $order_product_info['seller_id'],
                                'product_code' => $order_product_info['model']
                            );
                        }
                    }
                }
            }
        }

        if (!empty($order_product_data)) {
            self::_updateStockAfterAddItemInOrder($db, $order_product_data);
            $shipping_code = !empty($data_arr['shipping_code']) ? $data_arr['shipping_code'] : '';
            if ($data_arr['discount_type'] == 1 && $data_arr['stock_transfer'] != 1) {
                self::refreshOrCustomPaychargeDiscount($controller, $order_id, '', $data_arr);
            }
            if (strstr($shipping_code, 'weight')) {
                $shipping_details = json_decode(OrderEdit::getShippingDetails($db, $order_id, $suborder_id), true);
                $shipping_charge_arr = array();
                if (!empty($shipping_details['shipping_method']['weight']['quote'])) {
                    $shipping_charge_arr = array_combine(array_column($shipping_details['shipping_method']['weight']['quote'], 'code'), $shipping_details['shipping_method']['weight']['quote']);
                }

                self::changeShippingCharge($db, $shipping_charge_arr[$shipping_code]['cost'], $order_id, $suborder_id);
            }
            self::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id, true);
            $product_name_qty = '';
            foreach ($order_product_data as $key => $value) {
                $records_logs = array(
                    'order_id' => $order_id,
                    'suborder_id' => $suborder_id,
                    'key_id' => $value['order_product_id'],
                    'key_name' => 'order_product_id',
                    'edit_type' => 'ADD_PRODUCT',
                    'field_name' => 'edit row',
                    'old_value' => (int) $value['old_quantity'],
                    'new_value' => "quantity: " . $value['quantity'],
                    'user_id' => $data_arr['user_id'],
                    'comment' => '',
                    'name' => $data_arr['name'],
                    'user_name' => $data_arr['user_name']
                );

                self::saveEditHistory($db, $records_logs);
                $product_name_qty .= $value['product_code'] . " :- Sets: " . $value['quantity'] . "<br>";
            }
            $msg = 'Few products has been edited to this suborder: <br>' . $product_name_qty;
            $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $data_arr['order_status_id'] . "', '" . $db->escape($msg) . "', '" . $db->escape($data_arr['name']) . "',  '" . date('Y-m-d H:i:s') . "')");

            self::sendMailsForAddItemInSuborder($controller, $order_id, $suborder_id, $data_arr, $order_product_data);
        }
    }

    public static function _updateStockAfterAddItemInOrder($db, $order_product_data, $product_option_value_id = array()) {
        // Stock subtraction (also setting date_out_of_stock despite item going out of stock or not.
        // Cron checks for quantity before sending out of stock alerts, so no issues.

        foreach ($order_product_data as $order_product) {
            $sql = "UPDATE " . DB_PREFIX . "product
                SET quantity = GREATEST( 0 , quantity - " . (int) $order_product['quantity'] . " ),
                    date_out_of_stock = NOW()
                WHERE product_id = '" . (int) $order_product['product_id'] . "'
                  AND subtract = '1'";
            $db->query($sql);

            // Also subtracting Option quantity

            if (!empty($product_option_value_id['product_option_value_id'])) {
                $sql = "UPDATE " . DB_PREFIX . "product_option_value
                    SET quantity = GREATEST( 0 , quantity - " . (int) $order_product['quantity'] . " )
                    WHERE product_option_value_id = '" . (int) $product_option_value_id['product_option_value_id'] . "'
                      AND subtract = '1'";
                $db->query($sql);
            } else {
                $sql = "SELECT product_option_value_id FROM " . DB_PREFIX . "order_option
                WHERE order_product_id = '" . (int) $order_product['order_product_id'] . "'";
                $order_option_query = $db->query($sql);
                foreach ($order_option_query->rows as $option) {
                    $sql = "
                            UPDATE 
                                " . DB_PREFIX . "product_option_value
                            SET 
                                quantity = GREATEST( 0 , quantity - " . (int) $order_product['quantity'] . " )
                            WHERE 
                                product_option_value_id = '" . (int) $option['product_option_value_id'] . "'
                                AND subtract = '1'";
                    $db->query($sql);
                }
            }

            //Updating if there is assoiate product then update combo product 
            Product::updateComboProductQuantityUsingAssociate($db, $order_product['product_id']);
        }
    }

    public static function getShippingDetails($db, $order_id, $suborder_id) {

        $shipping_methods = self::getShippingMethodRequestArrayForSuborder($db, $order_id, $suborder_id);
        $url = HTTPS_CATALOG . 'api/checkout/shippingMethod';

        $url .= '&zone_id=' . $shipping_methods['zone_id'] . '&country_id=' . $shipping_methods['country_id'] . '&postcode=' . $shipping_methods['postcode'] . '&is_dropshipper=' . $shipping_methods['is_dropshipper'] . '&cartlimitcross=' . $shipping_methods['cartlimitcross'] . '&weight_cart=' . $shipping_methods['weight_cart'];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    public static function getEssShippingCharges($db, $order_id, $suborder_id) {

        $ess_shipping_charges = 0;
        $sql = "SELECT SUM(new_value) as ess_shipping_charges 
                FROM " . DB_PREFIX . "order_edit_history 
                WHERE edit_type = 'APPLY_ESS_SHIPPING' AND
                      order_id = '" . (int) $order_id . "' AND
                      suborder_id = '" . $db->escape($suborder_id) . "'";
        $query = $db->query($sql);
        if ($query->num_rows) {
            $ess_shipping_charges = $query->row['ess_shipping_charges'];
        }
        return $ess_shipping_charges;
    }

    /*
     * Public static function to get all edit suborder history edit_type wise
     * @author: Nilesh 
     */

    public static function getSuborderAllEditHistory($db, $order_id, $suborder_id, $edit_type = '') {
        $result = array();
        $sql = "SELECT * 
                FROM " . DB_PREFIX . "order_edit_history 
                WHERE order_id = '" . (int) $order_id . "' AND
                      suborder_id = '" . $db->escape($suborder_id) . "'";
        if (!empty($edit_type)) {
            $sql .= " AND edit_type = '" . $db->escape($edit_type) . "'";
        }

        $sql .= " ORDER BY edit_type";
        $query = $db->query($sql);

        if ($query->num_rows) {
            $result = $query->rows;
        }
        return $result;
    }

    /*
     * Public static function to get all edit suborder history edit_id wise
     * @author: Nilesh 
     */

    public static function getSuborderAllEditHistoryByEditId($db, $edit_id = array()) {
        $result = array();
        $sql = "SELECT * 
                FROM " . DB_PREFIX . "order_edit_history 
                WHERE 1 ";

        if (!empty($edit_id)) {
            $sql .= " AND edit_id IN (" . implode(',', $edit_id) . ")";
        }
        $sql .= " ORDER BY edit_type";
        $query = $db->query($sql);

        if ($query->num_rows) {
            $result = $query->rows;
        }
        return $result;
    }

    /**
     * Method for check buyer invoice is generated
     * @param : $controller : object of controller
     * @param : $order_id : Integer of order id
     * @return true or false
     * @author : vikas, Feb 2018
     */
    public static function checkBuyerInvoiceIsGenerated($controller, $order_id) {
        $sql = "SELECT osub.suborder_id 
                FROM " . DB_PREFIX . "suborder osub
                INNER JOIN  " . DB_PREFIX . "order o
                   ON osub.order_id = o.order_id  
                WHERE osub.order_id = " . (int) $order_id . "
                  AND osub.order_status_id !=2
                  AND osub.buyer_invoice_id > 0
                  AND o.payment_code = 'cod'
                Limit 1 ";
        $query = $controller->db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method for check Receipt Pay Id is greater than zero 
     * @param : $controller : object of controller
     * @param : $payment_id : Integer of payment id
     * @return true or false
     * @author : vikas, Feb 2018
     */
    public static function checkRecPayIdIsAvailable($controller, $payment_id) {
        $sql = "SELECT payment_id 
                FROM " . DB_PREFIX . "order_payment 
                WHERE payment_id = " . (int) $payment_id . "
                  AND rec_pay_id > 0 ";
        $query = $controller->db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method for check Receipt Pay Id is greater than zero 
     * @param : $controller : object of controller
     * @param : $payment_id : Integer of payment id
     * @return true or false
     * @author : vikas, Feb 2018
     */
    public static function checkPaymentIsSuccessfull($controller, $payment_id) {
        $sql = "SELECT payment_id 
                FROM " . DB_PREFIX . "order_payment 
                WHERE payment_id = " . (int) $payment_id . "
                  AND successfull = 1 ";
        $query = $controller->db->query($sql);
        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method for Edit Trxn Mode In Edit Order Payment
     * @param Controller $controller
     * @param array $data Array of parameters, i.e.{payment_id, order_id, action_comment, fields_data, selected_value}
     * @return NULL
     * @author : vikas, March 2018
     */
    public static function editTrxnModeInEditOrderPayment($controller, $data = array()) {

        $sql = "SELECT  order_no,
                        txn_status,
                        payment_mode,
                        payment_gateway,
                        amount,
                        txn_date_time,
                        reference,
                        txn_status,
                        successfull,
                        edit_history
                FROM " . DB_PREFIX . "order_payment
                WHERE payment_id = " . (int) $data['payment_id'];
        $query = $controller->db->query($sql);

        if ($query->num_rows) {

            $edit_history = array();
            $edit_history = unserialize($query->row['edit_history']);

            $user_id = $controller->user->getId();
            $name = $data['name'];
            $username = $data['user_name'];
            $user_type = $controller->db->escape($controller->user->getGroupName());
            $server = $_SERVER['HTTP_USER_AGENT'];
            $request_obj = new Request();
            $ip = $request_obj->getIpAddress;
            $date = date('Y-m-d H:i:s');
            $reference = $username . ' initiated [' . $data['selected_value'] . '] ' . ' ' . $query->row['reference'];

            $edit_history[] = array(
                'user_id' => $user_id,
                'name' => $name,
                'user_name' => $username,
                'user_type' => $user_type,
                'user_ip' => $ip,
                'user_agent' => $server,
                'date_added' => $date,
                'comment' => $data['action_comment']
            );

            $records_logs_map = array(
                'edit_history' => array(
                    'old_value' => $query->row['edit_history'],
                    'new_value' => serialize($edit_history),
                ),
                'reference' => array(
                    'old_value' => $query->row['reference'],
                    'new_value' => $reference,
                ),
            );

            if (!empty($data['fields_data'])) {
                foreach ($data['fields_data'] as $field_key => $field_value) {
                    $records_logs_map[$field_key] = array(
                        'old_value' => $query->row[$field_key],
                        'new_value' => strtoupper($field_value),
                    );
                }
            }

            // for update query in order_payment
            $update_query_string = '';
            foreach ($query->row as $key => $value) {
                if (array_key_exists($key, $records_logs_map)) {
                    // update query in order_payment
                    $update_query_string .= $key . " = '" . $controller->db->escape(trim($records_logs_map[$key]['new_value'])) . "', ";

                    $records_logs = array(
                        'order_id' => $data['order_id'],
                        'suborder_id' => '',
                        'key_id' => $data['payment_id'],
                        'key_name' => 'order_payment_id',
                        'edit_type' => 'ORDER_PAYMENT',
                        'field_name' => $key,
                        'old_value' => $records_logs_map[$key]['old_value'],
                        'new_value' => $records_logs_map[$key]['new_value'],
                        'user_id' => $user_id,
                        'comment' => '',
                        'name' => $data['name'],
                        'user_name' => $data['user_name']
                    );
                    self::saveEditHistory($controller->db, $records_logs);
                }
            }

            $sql = "UPDATE " . DB_PREFIX . "order_payment
                    SET " . rtrim($update_query_string, ' , ') . "
                    WHERE payment_id =  " . (int) $data['payment_id'];
            $controller->db->query($sql);

            $data_mail = array(
                'order_no'        => $query->row['order_no'],
                'txn_status'      => $data['selected_value'],
                'payment_mode'    => $data['fields_data']['payment_mode'],
                'payment_gateway' => $data['fields_data']['payment_gateway'],
                'amount'          => $query->row['amount'],
                'txn_date_time'   => $query->row['txn_date_time'],
                'reference'       => $reference,
                'action_comment'  => $data['action_comment'],
                'mail_subject'    => 'Trxn Mode Updated'
            );

            self::mailForPaymentCancelledToAdmin($controller, $data_mail);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Method for Edit Trxn Mode In Edit Order Payment
     * @param : payment_id : Integer of payment id,
     * @param : data : array of data i.e.{payment_id, order_id, action_comment, fields_data, selected_value}
     * @return NULL
     * @author : vikas, March 2018
     */
    public static function editTrxnRefInEditOrderPayment($controller, $data = array()) {

        $sql = "SELECT  order_no,
                        payment_gateway,
                        txn_status,
                        payment_mode,
                        merchant_txn_id,
                        amount,
                        txn_date_time,
                        reference,
                        txn_status,
                        successfull,
                        edit_history
                FROM " . DB_PREFIX . "order_payment
                WHERE payment_id = " . (int) $data['payment_id'];
        $query = $controller->db->query($sql);

        if ($query->num_rows) {

            $edit_history = array();
            $edit_history = unserialize($query->row['edit_history']);

            $user_id = $controller->user->getId();
            $name = $controller->user->getUserName($controller->user->getId())['name'];
            $username = $controller->user->getUserName($controller->user->getId())['username'];
            $user_type = $controller->db->escape($controller->user->getGroupName());
            $server = $_SERVER['HTTP_USER_AGENT'];
            $request_obj = new Request();
            $ip = $request_obj->getIpAddress;
            $date = date('Y-m-d H:i:s');
            $reference = $username . ' initiated [' . $data['selected_value'] . '] ' . ' ' . $query->row['reference'];

            $edit_history[] = array(
                'user_id' => $user_id,
                'name' => $name,
                'user_name' => $username,
                'user_type' => $user_type,
                'user_ip' => $ip,
                'user_agent' => $server,
                'date_added' => $date,
                'comment' => $data['action_comment']
            );

            $records_logs_map = array(
                'edit_history' => array(
                    'old_value' => $query->row['edit_history'],
                    'new_value' => serialize($edit_history),
                ),
                'reference' => array(
                    'old_value' => $query->row['reference'],
                    'new_value' => $reference,
                ),
            );

            if (!empty($data['fields_data'])) {
                foreach ($data['fields_data'] as $field_key => $field_value) {
                    $records_logs_map[$field_key] = array(
                        'old_value' => $query->row[$field_key],
                        'new_value' => $field_value,
                    );
                }
            }

            // for update query in order_payment
            $update_query_string = '';
            foreach ($query->row as $key => $value) {
                if (array_key_exists($key, $records_logs_map)) {
                    // update query in order_payment
                    $update_query_string .= $key . " = '" . $controller->db->escape(trim($records_logs_map[$key]['new_value'])) . "', ";

                    $records_logs = array(
                        'order_id' => $data['order_id'],
                        'suborder_id' => '',
                        'key_id' => $data['payment_id'],
                        'key_name' => 'order_payment_id',
                        'edit_type' => 'ORDER_PAYMENT',
                        'field_name' => $key,
                        'old_value' => $records_logs_map[$key]['old_value'],
                        'new_value' => $records_logs_map[$key]['new_value'],
                        'user_id' => $user_id,
                        'comment' => '',
                        'name' => $name,
                        'user_name' => $username
                    );
                    self::saveEditHistory($controller->db, $records_logs);
                }
            }

            $sql = "UPDATE " . DB_PREFIX . "order_payment
                    SET " . rtrim($update_query_string, ' , ') . "
                    WHERE payment_id =  " . (int) $data['payment_id'];
            $controller->db->query($sql);

            $data_mail = array(
                'order_no'        => $query->row['order_no'],
                'payment_gateway' => $query->row['payment_gateway'],
                'txn_status'      => $data['selected_value'],
                'payment_mode'    => $query->row['payment_mode'],
                'merchant_txn_id' => $data['fields_data']['merchant_txn_id'],
                'amount'          => $query->row['amount'],
                'txn_date_time'   => $query->row['txn_date_time'],
                'reference'       => $reference,
                'action_comment'  => $data['action_comment'],
                'mail_subject'    => 'Trxn Reference Updated'
            );

            self::mailForPaymentCancelledToAdmin($controller, $data_mail);

            return true;
        } else {
            return false;
        }
    }

    /**
     * updateSubOrderField
     * @param  : order_id, $suborder_ids, field, field_val
     * @return : success or false
     * @author : Devendra Dhayal, 04/04/2018
     */
    public static function updateSubOrderField($db, $order_id, $suborder_ids, $field, $field_val) {
        // only below fields can be updated through this method
        $editable_fields = array('no_wsb_tape', 'no_invoice_with_shipment');
        if (!in_array($field, $editable_fields)) {
            return false;
        }

        $sql = "UPDATE `" . DB_PREFIX . "suborder` SET " . $field . " = '" . (int) $field_val . "' 
                WHERE order_id = " . (int) $order_id . " AND suborder_id IN (" . $suborder_ids . ")";
        $result_query = $db->query($sql);
        return $result_query;
    }

    /**
     * updateOrderCustomer: used for change customer from order
     * @param  : order_id, $data
     * @return : success or false
     * @author : Nilesh Sharma, 2018
     */
    public static function updateOrderCustomer($controller, $order_id, $data) {
        $db = $controller->db;
        $records_logs_arr = array(
            'customer_id' => 'customer_id_for_edit',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'telephone' => 'telephone',
        );
        $result = array_intersect_key($data, $records_logs_arr);
        if (empty($result)) {
            return false;
        }
        $sql = "SELECT GROUP_CONCAT(os.suborder_id) AS suborder_ids,
                           oo.customer_id,
                           oo.firstname,
                           oo.lastname,
                           oo.email,
                           oo.telephone
                    FROM " . DB_PREFIX . "order oo INNER JOIN
                         " . DB_PREFIX . "suborder os ON oo.order_id=os.order_id
                    WHERE oo.order_id = '" . (int) $order_id . "'
                    GROUP BY oo.order_id";
        $order_info = $db->query($sql)->row;
        $sql = "SELECT customer_id,
                           firstname,
                           lastname,
                           email,
                           telephone
                    FROM " . DB_PREFIX . "customer
                    WHERE customer_id = '" . (int) $data['customer_id_for_edit'] . "'";
        $customer_info = $db->query($sql)->row;
        $records_logs = array();
        $msg_string_for_history = '';
        $update_sql = "UPDATE " . DB_PREFIX . "order SET order_no = order_no ";

        foreach ($records_logs_arr as $key => $value) {
            if (!empty($data[$key])) {
                if ($key == 'customer_id') {
                    $update_sql .= ", " . $key . " = '" . (int) $data[$value] . "'";

                    $msg_string_for_history .= ucfirst($key) . ' 
                                            OLD: ' . $order_info[$key] . '
                                            NEW: ' . $data[$value] . '<br>';
                    $records_logs[$key] = array(
                        'field_name' => $key,
                        'old_value' => $order_info[$key],
                        'new_value' => $data[$value]
                    );
                } else {
                    $update_sql .= ", " . $key . " = '" . $db->escape($customer_info[$key]) . "'";
                    $msg_string_for_history .= ucfirst($key) . ' 
                                            OLD: ' . $order_info[$value] . '
                                            NEW: ' . $customer_info[$value] . '<br>';
                    $records_logs[$key] = array(
                        'field_name' => $key,
                        'old_value' => $order_info[$key],
                        'new_value' => $customer_info[$value]
                    );
                }
            }
        }

        $update_sql .= " WHERE order_id = '" . (int) $order_id . "'";

        $is_update = $db->query($update_sql);
        if ($is_update) {

            foreach ($records_logs as $key => $value) {
                $value['key_id'] = $order_id;
                $value['key_name'] = 'order_id';
                $value['order_id'] = $order_id;
                $value['suborder_id'] = '';
                $value['edit_type'] = 'EDIT_CUSTOMER';
                $value['user_id'] = $data['user_id'];
                $value['comment'] = $data['comment'];
                $value['name'] = $data['name'];
                $value['user_name'] = $data['user_name'];
                self::saveEditHistory($db, $value);
            }

            if (!empty($order_info['suborder_ids'])) {
                $suborder_ids = explode(',', $order_info['suborder_ids']);
                $msg_string_for_history .= "<br>Comments: " . $data['comment'];
                foreach ($suborder_ids as $suborder_id) {
                    $suborder_history = OrderInfo::getLastSuborderHistoryInfo($db, $order_id, $suborder_id);
                    $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $suborder_history['order_status_id'] . "', 'Customer information edited- " . $msg_string_for_history . "' ,'" . $db->escape($data['name']) . "',  '" . date('Y-m-d H:i:s') . "')");
                }
            }

            return true;
        }
    }

    /**
     * Function refreshPaychargeDiscount used for recalculation paycharge discount, based on certain changed value in an order, such as product value(subtotal), payment method, change shipping etc.
     * This function called internally after various action, such as addProduct, removeProduct, updateProduct, changePaymentMethod(if apply discounts is selected)
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: true or false if update in order product 
     * @author Nilesh, 2018
     */
     public static function refreshOrCustomPaychargeDiscount($controller, $order_id, $order_product_arr = array(), $data = array(), $apply_only_membership_discount = false) {
      $db = $controller->db;
      $suborder_id = '';
      if (!empty($data['suborder_id'])) {
        $suborder_id = $data['suborder_id'];
      }
      if (empty($order_product_arr)) {
        $order_product_arr = self::getAllProductInfoAndOrderInfo($db, $order_id);
      }
      $discount_type = '';
      // check for membership
      $membership_info = array();
      if (!isset($order_product_arr['custom_paycharge_rate'])) {
        $master_ids = Customer::getMasterIdsByCustomerIds($db, $order_product_arr['customer_id']);
        if (!empty($master_ids)) {
          $master_id = $master_ids[0]['master_id'];
          $order_date = date('Y-m-d', strtotime($order_product_arr['date_added']));
          $membership_obj = new Membership($controller);
          $membership_info = $membership_obj->getMembershipInfoByMasterIdAndDate($master_id, $order_date);
        }
      }
      if (!empty($membership_info)) {
        $discount_type = 'membership';
        $payment_code = $order_product_arr['payment_code'];
        $payment_discount_type = 'prepaid_discount';
        
        if($payment_code == 'cod'){
            $payment_discount_type = 'cod_discount';
        }else if(in_array($payment_code, CREDIT_PAYMENT_CODES)){
            $payment_discount_type = 'credit_discount';
        }

        $discount_percent = $membership_info[$payment_discount_type];
        $update_arr = self::getApplicableMembershipDiscount($controller, $order_id, $order_product_arr, $discount_percent);
      } else if($apply_only_membership_discount) {
        // if $apply_only_membership_discount set to true and membership is not applicable on currenct order then return;
        return;
      } else {
        $discount_type = 'paycharge';
        $update_arr = self::getAllApplicablePaychargeDiscount($controller, $order_id, $order_product_arr);
      }

      if (empty($update_arr)) {
        return true; //$update_arr;
      }

      $is_updated = self::updateOrderProductForPaycharge($db, $order_id, '', $update_arr);
      self::updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id, true);
      $old_discount_rate = 0;
      $new_discount_rate = 0;
      $old_discount_breakup_his = $old_discount_rate;
      $edit_type = isset($order_product_arr['custom_paycharge_rate']) ? 'CUSTOM_APPLY_DISCOUNT' : 'REFRESH_DISCOUNT';
      foreach ($update_arr as $order_product_id => $value) {
        if (!empty($order_product_arr['order_product'][$order_product_id]['discount_breakup'])) {
          $olddiscount_breakup = unserialize($order_product_arr['order_product'][$order_product_id]['discount_breakup']);
          if (!empty($olddiscount_breakup[$discount_type]['valuep']) || (isset($olddiscount_breakup[$discount_type]['valuep']) && $olddiscount_breakup[$discount_type]['valuep'] == 0)) {
            $old_discount_rate = abs($olddiscount_breakup[$discount_type]['valuep']);
          }
          if (!empty($olddiscount_breakup['coupon'])) {
            $old_discount_breakup_his = $order_product_arr['order_product'][$order_product_id]['discount_breakup'];
          }
        }

        if (!empty($value['discount_breakup'])) {
          $newdiscount_breakup = unserialize($value['discount_breakup']);
          if (!empty($newdiscount_breakup[$discount_type]['valuep']) || (isset($newdiscount_breakup[$discount_type]['valuep']) && $newdiscount_breakup[$discount_type]['valuep'] == 0)) {
            $new_discount_rate = abs($newdiscount_breakup[$discount_type]['valuep']);
          }
        }
        $comment = !empty($order_product_arr['custom_paycharge_comment']) ? $order_product_arr['custom_paycharge_comment'] : '';
        $old_dis_rate = $old_discount_rate;
        if ($old_discount_rate == 0 && !empty($old_discount_breakup_his)) {
          $old_discount_rate = $old_discount_breakup_his;
        }
        $records_logs = array(
          'order_id' => $order_id,
          'suborder_id' => $suborder_id,
          'key_id' => $order_product_id,
          'key_name' => 'order_product_id',
          'edit_type' => $edit_type,
          'field_name' => ($discount_type == 'paycharge') ? 'Paycharge' : 'Membership' . ' Discount Rate',
          'old_value' => $old_discount_rate,
          'new_value' => $new_discount_rate,
          'user_id' => $data['user_id'],
          'comment' => $comment,
          'name' => $data['name'],
          'user_name' => $data['user_name']
        );

        self::saveEditHistory($controller->db, $records_logs);
      }
      if (!empty($suborder_id)) {
        $suborder_history = OrderInfo::getLastSuborderHistoryInfo($db, $order_id, $suborder_id);
        $msg = ($discount_type == 'paycharge') ? 'Paycharge' : 'Membership' . ' Discount rate changed: <br>' . 'OLD: ' . $old_dis_rate . '
          NEW: ' . $new_discount_rate . '<br> For more information check log';
        if (!empty($comment)) {
          $msg .= "<br> Comment: " . $comment;
        }
        $db->query("INSERT INTO " . DB_PREFIX . "order_history (order_id, suborder_id, order_status_id, notes, user, date_added) VALUES ('" . (int) $order_id . "', '" . $db->escape($suborder_id) . "', '" . (int) $suborder_history['order_status_id'] . "', '" . $db->escape($msg) . "', '" . $db->escape($data['name']) . "',  '" . date('Y-m-d H:i:s') . "')");
      }

      return $update_arr;
    }

    public static function updateOrderProductForPaycharge($db, $order_id, $suborder_id = '', $data = array()) {
        if (!empty($data)) {
            foreach ($data as $order_product_id => $order_product) {
                $sql = "UPDATE " . DB_PREFIX . "order_product 
                        SET discount_per_piece = '" . (float) $order_product['discount_per_piece'] . "',
                            discount_breakup = '" . $db->escape($order_product['discount_breakup']) . "'
                        WHERE order_product_id='" . (int) $order_product_id . "' AND
                              order_id = '" . (int) $order_id . "'";
                if (!empty($suborder_id)) {
                    $sql .= " AND suborder_id = '" . $db->escape($suborder_id) . "'";
                }
                $db->query($sql);
            }
            return true;
        }
    }

    /**
     * Function getAllProductInfoAndOrderInfo user for refreshPaychargeDiscount for product info
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: array of products and order info
     * @author Nilesh, 2018
     */
    public static function getAllProductInfoAndOrderInfo($db, $order_id, $order_info = array()) {

        if (empty($order_info)) {
            $selector = array(
                'order' => array(
                    'select' => array(
                        'customer_id',
                        'payment_code',
                        'stock_transfer',
                        'date_added'
                    ),
                ),
                'suborder' => array(
                    'select' => array(
                        'order_status_id',
                        'buyer_invoice_id',
                        'invoice_no',
                        'shipping_code'
                    ),
                ),
                'order_product' => array()
            );
            $order_info = OrderInfo::getOrderInfo($db, $order_id, '', $selector);
        }
        $suborder_info = $order_info['suborder'];
        $order_info = $order_info['order'];

        $overall_subtotal = 0;
        $order_wise_product_info = array();
        $coupon_discount = false;
        $membership_discount = false;
        foreach ($suborder_info as $suborder_id => $suborder) {
            if (($suborder['order_status_id'] > 0) &&
                    ($suborder['order_status_id'] != 2)) {
                if (!empty($suborder['order_product'])) {
                    foreach ($suborder['order_product'] as $order_product) {

                        if (!empty($order_product['discount_breakup'])) {
                            $discount_breakup_arr = unserialize($order_product['discount_breakup']);
                            if (!empty($discount_breakup_arr['coupon'])) {
                                $coupon_discount = true;
                            }
                            if (!empty($discount_breakup_arr['membership'])) {
                                $membership_discount = true;
                            }
                        }
                        if ($order_product['edit_type'] != 'CANCELLED_BY_CUSTOMER') {
                            if (((int) $order_product['buyer_invoice_id'] == 0) &&
                                    ((int) $suborder['buyer_invoice_id'] == 0) &&
                                    ((int) $suborder['invoice_no'] == 0)) {
                                $is_discount = 1;
                                if ($suborder['shipping_code'] == 'weight.weight_0' ||
                                        $suborder['shipping_code'] == 'store_pickup') {
                                    $is_discount = 0;
                                }
                                $order_product_id = $order_product['order_product_id'];
                                $order_wise_product_info[$order_product_id] = array(
                                    'product_id' => $order_product['product_id'],
                                    'name' => $order_product['name'],
                                    'model' => $order_product['model'],
                                    'quantity' => $order_product['quantity'],
                                    'piece_in_set' => $order_product['piece_in_set'],
                                    'price_per_piece' => $order_product['price_per_piece'],
                                    'discount_per_piece' => $order_product['discount_per_piece'],
                                    'discount_breakup' => $order_product['discount_breakup'],
                                    'is_discount' => $is_discount
                                );
                                $overall_subtotal += ((float) $order_product['price_per_piece'] * (int) $order_product['quantity'] * (int) $order_product['piece_in_set']);
                            } elseif (((int) $order_product['buyer_invoice_id'] > 0) &&
                                    ((int) $suborder['buyer_invoice_id'] > 0) &&
                                    ((int) $suborder['invoice_no'] > 0)) {
                                $overall_subtotal += ((float) $order_product['price_per_piece'] * (int) $order_product['quantity'] * (int) $order_product['piece_in_set']);
                            }
                        }
                    }
                }
            }
        }
        
        $final_array = array(
            'customer_id' => $order_info['customer_id'],
            'stock_transfer' => $order_info['stock_transfer'],
            'payment_code' => $order_info['payment_code'],
            'date_added' => $order_info['date_added'],
            'order_subtotal' => $overall_subtotal,
            'coupon_discount' => $coupon_discount,
            'membership_discount' => $membership_discount,
            'order_product' => $order_wise_product_info
        );
        return $final_array;
    }

    /**
     * Function getAllApplicablePaychargeDiscount used for getting paycharge discout array if applicable
     * @params:
     * $db - Database object (required)
     * $order_id - Order ID (int, required)
     * @return: array of products and order info
     * @author Nilesh, 2018
     */
    public static function getAllApplicablePaychargeDiscount($controller, $order_id, $order_product_arr, $suborder_id = '') {
        $db = $controller->db;
        $update_arr = array();
        if (isset($order_product_arr['custom_paycharge_rate']) && $order_product_arr['custom_paycharge_rate'] > 0) {
            $update_arr = self::_setPaychargeDiscountWithCustomOrAvailableRate($controller, $order_product_arr);
        } elseif (isset($order_product_arr['custom_paycharge_rate']) && $order_product_arr['custom_paycharge_rate'] == 0) {
            $update_arr = self::_setPaychargeDiscountWithZero($order_product_arr);
        } else {
            
            require_once(DIR_SYSTEM . 'library/total/paycharge.php');

            $paychange = new Paycharge($controller);
            $paychange->setOptions('custom_call', true);
            $paychange->setOptions('customer_id', $order_product_arr['customer_id']);
            $paychange->setOptions('sub_total', $order_product_arr['order_subtotal']);
            $paychange->setOptions('payment_method_code', $order_product_arr['payment_code']);
            $paychange->setOptions('cart_products', $order_product_arr['order_product']);
            $paychange->setOptions('cart_data', $order_product_arr['order_product']);
            $discount_percent = $paychange->getPaychargeDiscountPercent();
           
            //check if store_pickup discount applicable or not
            $order_product_dic_arr = self::_filterOrderPorductIsValidForDiscountOrNot($order_product_arr);
            if (count($order_product_dic_arr) > 1 && (isset($order_product_arr['stock_transfer']) && !$order_product_arr['stock_transfer'])) {
                $order_product_arr['order_product'] = $order_product_dic_arr['not_applicable'];
                $not_applicable_arr = self::_setPaychargeDiscountWithZero($order_product_arr);
                $order_product_arr['order_product'] = $order_product_dic_arr['applicable'];
                $applicable_arr = self::_setPaychargeDiscountWithCustomOrAvailableRate($controller, $order_product_arr, $discount_percent);
                $update_arr = $not_applicable_arr + $applicable_arr;
            } else {
                if (!$discount_percent['discount_percent'] || (isset($order_product_arr['stock_transfer']) && $order_product_arr['stock_transfer'])) {
                    $update_arr = self::_setPaychargeDiscountWithZero($order_product_arr);
                } else {
                    $update_arr = self::_setPaychargeDiscountWithCustomOrAvailableRate($controller, $order_product_arr, $discount_percent);
                }
            }
        }

        return $update_arr;
    }

    /**
     * Function getApplicableMembershipDiscount used for getting membership discount
     * @params:
     * $controller, order id, order product data, membership discount
     * @return: array of products and order info
     * @author Devendra, September 2018
     */
    public static function getApplicableMembershipDiscount($controller, $order_id, $order_product_arr, $discount_percent) {
        if (empty($order_product_arr['order_product'])) {
          return array();
        }
        
        $update_arr = array();

        foreach ($order_product_arr['order_product'] as $order_product_id => $order_product) {
            $discount_breakup = array();
            if (!empty($order_product['discount_breakup'])) {
                $discount_breakup = unserialize($order_product['discount_breakup']);
            }
            //If paycharge discount is already applied it will replaced by membership discount
            if (!empty($discount_breakup['paycharge'])) {
                unset($discount_breakup['paycharge']);
            }
            $discount_breakup['membership']['title'] = 'Membership Discount (-'. (int)$discount_percent .'%)';;
            $discount_breakup['membership']['valuep'] = $discount_percent;
            $discount_per_piece = round(
                    $order_product['price_per_piece'] / 100 * (float) $discount_percent, (int) $controller->currency->getDecimalPlace()
            );
            $discount_breakup['membership']['value'] = (-1) * (float) $discount_per_piece;
            $discount_per_piece = 0;

            foreach ($discount_breakup as $discount) {
                $discount_per_piece += (float) $discount['value'];
            }
            $update_arr[$order_product_id] = array(
                'discount_per_piece' => (float) $discount_per_piece,
                'discount_breakup' => serialize($discount_breakup),
            );
        }

        return $update_arr;
    }

    /**
     * Function private _setPaychargeDiscountWithZero used for set paycharge discount as zero accourding to $order_product_arr
     * @params:
     * $order_product_arr - order product which has to set paycharge discount zero
     * @return: array of order product with dicount columns for update 
     * @author Nilesh, 2018
     */
    private static function _setPaychargeDiscountWithZero($order_product_arr) {

        $data = array();
        if (!empty($order_product_arr['order_product'])) {
            foreach ($order_product_arr['order_product'] as $order_product_id => $order_product) {
                if (!empty($order_product['discount_breakup'])) {
                    $discount_per_piece = 0;
                    $discount_breakup = unserialize($order_product['discount_breakup']);
                    unset($discount_breakup['paycharge']);
                    unset($discount_breakup['coupon']);
                    unset($discount_breakup['membership']);
                    
                    foreach ($discount_breakup as $discount) {
                        $discount_per_piece += (float) $discount['value'];
                    }
                    $data[$order_product_id] = array(
                        'discount_per_piece' => "$discount_per_piece",
                        'discount_breakup' => serialize($discount_breakup),
                    );
                }
            }
        }
        return $data;
    }

    /**
     * Function private _setPaychargeDiscountWithCustomOrAvailableRate used for set paycharge discount as custom paycharge rate or available rate with $order_product_arr
     * @params:
     * $controller - Controller obj (required)
     * $order_product_arr - order product which has to set paycharge discount with give discount rate
     * @return: array of order product with dicount columns for update 
     * @author Nilesh, 2018
     */
    private static function _setPaychargeDiscountWithCustomOrAvailableRate($controller, $order_product_arr, $discount_percent = array()) {
        $data = array();
        $valuep = 0;
        $rate = 0;
        
        require_once(DIR_SYSTEM . 'library/total/paycharge.php');
        $paychange = new Paycharge($controller);

        if (!empty($discount_percent)) {
            $payment_method = $discount_percent['paychange_data']['payment_method'];
            $amount = $discount_percent['paychange_data']['amount'];
            $valuep = $discount_percent['paychange_data']['valuep'];
            $rate = abs($discount_percent['discount_percent']);
        } else {
            $payment_method = $order_product_arr['payment_code'];
            $amount = $order_product_arr['order_subtotal'];

            if (!empty($order_product_arr['custom_paycharge_rate'])) {
                $valuep = (-1) * abs($order_product_arr['custom_paycharge_rate']);
                $rate = abs($order_product_arr['custom_paycharge_rate']);
            }
        }

        if (!empty($order_product_arr['order_product']) && $rate > 0) {
            foreach ($order_product_arr['order_product'] as $order_product_id => $order_product) {

                $discount_breakup = array();
                $discount_per_piece = 0;

                if(!$paychange->isProductIdExistInExceptionRules($order_product['product_id'])){

                    $discount_breakup['paycharge']['payment_method'] = $payment_method;
                    $discount_breakup['paycharge']['valuep'] = $valuep;
                    $discount_breakup['paycharge']['amount'] = $amount;
                    $discount_per_piece = (-1) * round(
                            $order_product['price_per_piece'] / 100 * (float) $rate, (int) $controller->currency->getDecimalPlace()
                    );
                    $discount_breakup['paycharge']['value'] = $discount_per_piece;
                }

                $data[$order_product_id] = array(
                    'discount_per_piece' => $discount_per_piece,
                    'discount_breakup' => serialize($discount_breakup),
                );
            }
        }
        return $data;
    }

    /**
     * Function filterOrderPorductIsValidForDiscount is to check wheather order product is applicable for discount like if store pickup
     * @author Nilesh, 2018
     */
    private static function _filterOrderPorductIsValidForDiscountOrNot($order_product) {
        $return_arr = array();
        if (empty($order_product['order_product'])) {
            return $return_arr;
        }

        foreach ($order_product['order_product'] as $order_product_id => $value) {
            if (isset($value['is_discount']) && $value['is_discount'] == 0) {
                $return_arr['not_applicable'][$order_product_id] = $value;
            } else {
                $return_arr['applicable'][$order_product_id] = $value;
            }
        }
        return $return_arr;
    }

    /**
     * Funtion getOrderProductComboSiblings to filter Order product array to combo product
     * @author: Nilesh, 2018
     */
    public static function getOrderProductComboSiblings($order_products, $is_seller_invoice_check = false) {
        $combo_product_map = array();
        $order_product_siblings_map = array();
        foreach ($order_products as $key => $product) {
            if ((int) $product['product_id'] !== (int) $product['combo_product_id']) {

                if ($is_seller_invoice_check) {
                    if ((int) $product['seller_invoice_id'] == 0) {
                        $order_product_siblings_map[$product['order_product_id']] = $product['combo_product_id'];
                        $combo_product_map[$product['combo_product_id']][$product['order_product_id']] = array();
                    }
                } else {
                    if ($product['edit_type'] == 'YES' || $product['edit_type'] == 'SELLER_LATER_DISPATCH') {
                        $order_product_siblings_map[$product['order_product_id']] = $product['combo_product_id'];
                        $combo_product_map[$product['combo_product_id']][$product['order_product_id']] = array();
                    }
                }
            }
        }
        if (!empty($order_product_siblings_map)) {
            foreach ($order_product_siblings_map as $key => $value) {
                $order_product_siblings_map[$key] = array_keys($combo_product_map[$value]);
            }
        }

        return $order_product_siblings_map;
    }

    /**
     * Function updateProductStocksByOrderHistory is to update product stock on changing order history
     * @param (int) $old_order_status_id 
     * @param (int) $order_status_id (new) 
     * @param (array) $order_product_info order products info array
     * @param (array) $order_product_option_info order products option info array
     * @author Nilesh, 2018
     */
    public static function updateProductStocksByOrderHistory($db, $old_order_status_id, $order_status_id, $order_product_info, $order_product_option_info) {

        $is_product_qty_increase = 0;
        $is_product_qty_decrease = 0;

        $seller_ids_arr_check = array();
        //Check seller invoice generate
        $seller_id_arr = array_values(array_unique(array_column($order_product_info, 'seller_id')));
        
        foreach ($seller_id_arr as $seller_ids) {
            $check_seller_invoice_generate = SellerInfo::checkSellerInvoiceToBeGenerated($db, $seller_ids);
            if (empty($check_seller_invoice_generate)) {
                $seller_ids_arr_check[] = $seller_ids;
            }
        }

        if(!empty($seller_ids_arr_check)){
        
            //if order is cancelled product stock will increase or decrease
            if (
                (int)$old_order_status_id != (int)ORDER_STATUS['Canceled']
                && (int)$order_status_id == (int)ORDER_STATUS['Canceled']
            ) {
                $is_product_qty_increase = 1;
            }elseif(
                (int)$old_order_status_id == (int)ORDER_STATUS['Canceled']
                && (int)$order_status_id != (int)ORDER_STATUS['Canceled']
            ) { //if order is revival product stock will decrease

                $is_product_qty_decrease = 1;
                $suborder_id = array_unique(array_column($order_product_info, 'suborder_id'));
                $suborder_id = $suborder_id[0] ?? '';

                //Check is any product belongs to suborder, having stock to decrease
                if(!self::checkProductQtyToDecrease($db, $suborder_id)){
                    echo "No product's Quantity available to reduce, when marking suborder as processed from canceled!!";
                    return false;
                }
            }

            $product_ids = array_unique(array_column($order_product_info, 'product_id'));
            $product_ids = implode(',', $product_ids);

            $product_qty = Product::getProductQtyWithOptionQty($db, $product_ids);

            foreach ($order_product_info as $opid => $order_product) {
                if ($order_product['edit_type'] == 'YES' ||
                        $order_product['edit_type'] == 'SELLER_LATER_DISPATCH' ||
                        $order_product['edit_type'] == 'SELLER_APPROVED') {

                    if ( !(in_array($order_product['seller_id'], $seller_ids_arr_check))  ) {
                        continue;
                    }

                    $product_option = array();
                    if (!empty($order_product_option_info[$opid])) {
                        $product_option['product_option_value_id'] = $order_product_option_info[$opid]['product_option_value_id'];
                    }

                    if ($is_product_qty_increase == 1) {
                        self::updateProductStock($db, $order_product['product_id'], $order_product['seller_id'], $order_product['quantity'], $product_option);
                    } elseif (
                        $is_product_qty_decrease == 1 
                            &&
                        self::checkExistingStockToDecrease($db, $order_product, $product_qty)
                    ) { //increase=0 i.e. stock down 
                        self::_updateStockAfterAddItemInOrder($db, array($order_product), $product_option);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Public method to check for all products in given suborder_id, availble stock to down product with or without option
     * @param: DB $db, String $suborder_id
     * @return Bool
     * @author: Nishu, Nov 2018
    */
    public function checkProductQtyToDecrease($db, string $suborder_id): bool{
        $is_valid = false;

        if( !empty($suborder_id) ){
            $sql = "
                    SELECT 
                        COUNT(op.order_product_id) AS row_update
                    FROM
                        oc_order_product AS op
                            INNER JOIN
                        oc_ms_seller AS ms ON ms.seller_id = op.seller_id
                            INNER JOIN
                        oc_product AS p ON p.product_id = op.product_id
                            LEFT JOIN
                        oc_order_option AS oo ON oo.order_product_id = op.order_product_id
                            LEFT JOIN
                        oc_product_option_value AS pov ON pov.product_id = p.product_id
                            AND oo.product_option_value_id = pov.product_option_value_id
                    WHERE
                        ms.seller_invoice_generate = 0
                            AND p.quantity > 0
                            AND (pov.quantity IS NULL OR pov.quantity > 0)
                            AND op.suborder_id = '". $db->escape($suborder_id) ."'";

            $result = $db->query($sql);
            if($result->num_rows > 0 && $result->row['row_update'] > 0){
                $is_valid = true;
            }
        }
        
        return $is_valid;
    }

    /**
     * Public method to check availble stock to down product with or without option
     * @param: $order_product: order_product detail to how much sotck we have to decrease
     *          $product_qty :          
     * @return Bool
     * @author: Nishu, Nov 2018
    */
    public function checkExistingStockToDecrease($db, array &$order_product, array $all_product_qty): bool{
        $is_valid = false;

        if( !empty($order_product) && !empty($all_product_qty) ){
            foreach ($all_product_qty as $key => $value) {

                if(
                    $value['product_id'] == $order_product['product_id'] && 
                    $value['piece_in_set'] == $order_product['piece_in_set'] && 
                    $value['product_option_value_id'] == $order_product['product_option_value_id'] &&
                    $value['product_total_qty'] > 0 &&
                    (empty($value['option_qty']) || $value['option_qty'] > 0)

                ){
                    //Check if product option exist and not proper qauntity available for revivle
                    if(
                        $value['product_total_qty'] < $order_product['quantity'] ||
                        (
                            !empty($value['option_qty']) && 
                            $value['option_qty'] < $order_product['quantity']
                        )
                    ){
                        //Set order_product's quantity to minimum quantity
                        $order_product['quantity'] = !empty($value['option_qty']) ? $value['option_qty'] : $value['product_total_qty'];
                        $order_product_id          = $order_product['order_product_id'];
                        $order_product['edit_type'] = 'CANCELLED_BY_CUSTOMER';
                        
                        //Split OrderProduct with
                        $new_opid = self::splitOrderProductWithoutEffectPieceInSet($db, $order_product);
                    }


                    $is_valid = true;
                }
            }
        } 
        
        return $is_valid;
    }

    /**
     * Function updateOrderTotalsDueVariousAction is to update suborder and order total on changing order histories from store procedure. This method purpose is to remove triggers from suborder table
     * @param (varchar) $suborder_id 
     * @author Nilesh, 2018
     */
    public static function updateOrderTotalsDueVariousAction($db, $order_id, $suborder_id = '', $all_suborder_check = false) {

        //if $all_suborder_check is true then update all suborder total of order
        if ($all_suborder_check || empty($suborder_id)) {
            $suborder_id_arr = self::getAllSuborderFromOrder($db, $order_id);
            if (!empty($suborder_id_arr)) {
                foreach ($suborder_id_arr as $suborder_ids) {
                    $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_ids) . "')");
                }
            }
        } else {
            $db->query("CALL updateTotalInvoiceAmount('" . $db->escape($suborder_id) . "')");
        }
        $db->query("CALL updateOperationsStatusOfOrder('" . (int) $order_id . "')");
    }

    /**
     * Function getAllSuborderFromOrder is used to get all suborder_id of order
     * @param (varchar) $suborder_id 
     * @author Nilesh, 2018
     */
    public static function getAllSuborderFromOrder(Database\DB $db, int $order_id): array {
        $result = array();
        $sql = "SELECT suborder_id 
                FROM " . DB_PREFIX . "suborder 
                WHERE order_id = '" . (int) $order_id . "'";
        $suborder_id_query = $db->query($sql);
        if ($suborder_id_query->num_rows) {
            $result = array_column($suborder_id_query->rows, 'suborder_id');
        }
        return $result;
    }

    /**
     * Function checkIfOrderCanBeCancelled is used to check wheathe a suborder is applicable for cancelled, it is when there is no order product edit_type in 'YES', 'SELLER_PARTIAL', 'SELLER_APPROVED'
     * @param (int) $order_id 
     * @param (varchar) $suborder_id 
     * @param array $order_product_id default empty if not empty so these order product ids are not consider in searching 
     * @return (bolean) true if applicable for cancelled else false 
     * @author Nilesh, 2018
     */
    public static function checkIfOrderCanBeCancelled($db, $order_id, $suborder_id, $order_product_id = array()) {
        $sql = "SELECT order_product_id 
                FROM " . DB_PREFIX . "order_product
                WHERE edit_type IN ('YES', 'SELLER_PARTIAL', 'SELLER_APPROVED', 'SELLER_LATER_DISPATCH') AND
                      order_id = '" . (int) $order_id . "' AND
                      suborder_id = '" . $db->escape($suborder_id) . "'";
        if (!empty($order_product_id)) {
            $sql .= " AND order_product_id NOT IN (" . implode(',', $order_product_id) . ")";
        }

        $sql .= " LIMIT 1";
        $query = $db->query($sql);

        if ($query->num_rows) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function updateGstNoOfOrder used to update gst no of an order
     * @param (int) $order_id  
     * @return (bolean) true if successful else false 
     * @author Nilesh, 2018
     */
    public static function updateGstNoOfOrder($db, $order_id, $gst_number) {
        $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number  = trim($gst_number);
        
        $sql = "UPDATE " . DB_PREFIX . "order
                SET gst_number = '" . $db->escape($gst_number) . "'
                WHERE order_id ='" . (int) $order_id . "'";
        $query = $db->query($sql);
    }
}

?>
