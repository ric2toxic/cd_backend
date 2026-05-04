<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ModelSaleOrder extends Model {

    /**
     * [discount_types -- for identifying the various discounts types for Calculating discount]
     * @var array
     */
    private $_discount_types = array(
        'paycharge',
        'coupon',
        'cashback',
        'discount',
        'deal_discount'
    );

    // get tracking url by perticular suborder  id
    public function getTrackingUrl($suborder_id_courier_partner) {
        if (!empty($suborder_id_courier_partner)) {
            $sql = "SELECT tracking_url
                    FROM " . DB_PREFIX . "courier_partners
                    WHERE courier_name LIKE '" . $this->db->escape($suborder_id_courier_partner) . "'";
            $query = $this->db->query($sql);

            $tracking_url = $query->num_rows ? $query->row['tracking_url'] : '';
        } else {
            $tracking_url = '';
        }
        return $tracking_url;
    }

    public function getOrderNo($order_id) {
        $order_query = $this->db->query("SELECT order_no FROM " . DB_PREFIX . "order WHERE order_id = '" . (int) $order_id . "' AND franchise_id = 0 ");
        if ($order_query->num_rows) {
            return $order_query->row['order_no'];
        } else {
            return false;
        }
    }

    public function getOrderAdvance($order_id) {
        $order_query = $this->db->query("SELECT value FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' AND code = 'advance'");
        if ($order_query->rows) {
            return abs((float) ($order_query->row['value']));
        } else {
            return false;
        }
    }

    private function _splitAmountAccSubtotal($amount, $order_data) {
        // initializing return data array
        $splitted_amount = array();
        $balance_for_last = $amount;
        $overall_subtotal = 0;

        foreach ($order_data['order_total'] as $order_total) {

            // if the value is subtotal, adding to overall as well as setting into the splitted array
            if ($order_total['code'] == 'sub_total') {
                $overall_subtotal += (float) $order_total['value'];
                $splitted_amount[$order_total['suborder_id']] = (float) $order_total['value'];
            }
        }

        // Now splitting upto the second last element. Last element value will be the balance left
        // This is done because we are using round to keep the numbers whole
        // first find the end key
        end($splitted_amount);
        $last_key = key($splitted_amount);
        reset($splitted_amount);

        foreach ($splitted_amount as $suborder_id => $value) {
            // ignore we are at last key-value pair
            if ($suborder_id != $last_key) {
                $splitted_amount[$suborder_id] = round($amount * $value / $overall_subtotal);
                $balance_for_last -= $splitted_amount[$suborder_id];
            }
        }

        $splitted_amount[$last_key] = $balance_for_last;

        return $splitted_amount;
    }

    public function getOrders($data = array()) {
        $join = '';
        $whr  = '';

        // Sales staff filter
        if (!empty($data['filter_sales_staff_id']) && (int)$data['filter_sales_staff_id'] > 0) {
            $join .= " INNER JOIN " . DB_PREFIX . "order_sales_staff oss
                        ON oss.order_id = o.order_id 
                           AND oss.sales_staff_id = ". (int)$data['filter_sales_staff_id'];
        }

        // Zone Area filter
        if (!empty($data['filter_sales_zone'])) {
            $join .= " INNER JOIN " . DB_PREFIX . "zone oz
                        ON o.payment_zone_id = oz.zone_id 
                           AND oz.zone_area LIKE '" . $this->db->escape($data['filter_sales_zone']) . "'";
        }

        // Customer Referral Code filter
        if (!empty($data['filter_referral_code'])) {
            $join .= " INNER JOIN " . DB_PREFIX . "customer c
                        ON c.customer_id = o.customer_id 
                           AND c.referral_code = '" . $this->db->escape($data['filter_referral_code']) . "'";
        }

        // Customer SMS Log filter
        if (!empty($data['filter_sms_log'])) {
            $filter_sms_log_type = ($data['filter_sms_log'] === 'mswipe' ? 'pos' : $data['filter_sms_log']);
            $join .= " INNER JOIN " . DB_PREFIX . "wsb_customer_to_sms_criteria_sync wps 
                        ON wps.customer_id = o.customer_id 
                           AND wps.criteria = '" . $this->db->escape($filter_sms_log_type) . "'";
        }

        // Suborder related filter
        $sub_join = " INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id ";
        $fos = array_merge(explode(',', $data['filter_order_status'] ?? ''),
                           explode(',', $data['order_pickup'] ?? ''),
                           explode(',', $data['order_transit'] ?? ''),
                           explode(',', $data['order_tantative'] ?? ''));
        $filter_order_status = array_unique(array_map('intval', array_filter($fos, 'strlen')));
        if ( !empty($filter_order_status) ) {
            $sub_join .= " AND osub.order_status_id IN (" . implode(',', $filter_order_status) . ")";
        } else {
            $sub_join .= " AND osub.order_status_id > 0";
        }

        $filter_pickup_city_code = trim($data['filter_pickup_city_code'] ?? $data['branch_code'] ?? '');
        if (!empty($filter_pickup_city_code)) {
            $sub_join .= " AND osub.suborder_id LIKE '%" . $this->db->escape($filter_pickup_city_code) . "%'";
        }

        if (!empty($data['filter_courier'])) {
            $sub_join .= " AND osub.shipping_method LIKE '%" . $this->db->escape($data['filter_courier']) . "%'";
        }

        if (!empty($data['filter_tracking_no'])) {
            $sub_join .= " AND osub.tracking_no LIKE '" . $this->db->escape($data['filter_tracking_no']) . "'";
        }

        // Order Table filters
        $filter_order_no = trim($data['filter_order_no'] ?? '');
        if ( !empty($filter_order_no) ) {
            $filter_order_no = trim(substr($filter_order_no, 0, 11));
            $order_no_len = strlen($filter_order_no);

            switch($order_no_len) {
                case 11: $whr .= " AND o.order_no LIKE '" . $this->db->escape($filter_order_no) . "'"; break;
                case  5: $whr .= " AND o.order_no_last_five_digits LIKE '" . $this->db->escape($filter_order_no) . "'"; break;
                default: $whr .= " AND o.order_no LIKE '" . $this->db->escape($filter_order_no) . "%'"; break;
            }
        }

        if (!empty($data['filter_city'])) {
            $fts_str = getFullTextSearchString($data['filter_city']);
            $whr .= " AND MATCH(o.shipping_city) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if (!empty($data['filter_company'])) {
            $fts_str = getFullTextSearchString($data['filter_company']);
            $whr .= " AND MATCH(o.shipping_company) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if (!empty($data['filter_customer'])) {
            $fts_str = getFullTextSearchString($data['filter_customer']);
            $whr .= " AND MATCH(o.firstname, o.lastname, o.email, o.telephone) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if (!empty($data['filter_date_added'])) {
            $whr .= " AND o.date_added >= '" . $this->db->escape($data['filter_date_added']) . " 00:00:00'";
            $whr .= " AND o.date_added <= '" . $this->db->escape($data['filter_date_added']) . " 23:59:59'";
        }

        if (isset($data['filter_total_low'])) {
            $whr .= " AND o.total >= '" . (float) $data['filter_total_low'] . "'";
        }

        if (isset($data['filter_total_high'])) {
            $whr .= " AND o.total <= '" . (float) $data['filter_total_high'] . "'";
        }

        if (isset($data['filter_store_list']) && !is_null($data['filter_store_list'])) {
            $whr .= " AND o.store_id = '" . (int) $data['filter_store_list'] . "'";
        } else {
            $whr .= " AND o.store_id IN (" . WSB_STORES_ID . ") ";
        }

        if (!empty($data['filter_customer_id'])) {
            $customer_ids_arr = explode(",", $data['filter_customer_id']);
            $customer_ids_arr = array_unique(array_filter(array_map('intval', $customer_ids_arr)));
            if (!empty($customer_ids_arr)) {
                $whr .= " AND o.customer_id IN ( " . implode(",", $customer_ids_arr) . ")";
            }
        }

        if (!empty($data['filter_payment_code'])) {
            $whr .= " AND o.payment_code LIKE '" . $this->db->escape($data['filter_payment_code']) . "'";
        }

        if (!empty($data['filter_gst_number'])) {
            $whr .= " AND o.gst_number LIKE '" . $this->db->escape(trim($data['filter_gst_number'])) . "'";
        }

        if (!empty($data['filter_good_process'])) {
            $whr .= " AND o.operations_status = '" . $this->db->escape($data['filter_good_process']) . "'";
        }

        // Pagination related filter condition
        $p = $data['page'] ?? '';
        if ( $p !== 'FIRST' && (int)$p > 0 ) {
            $whr .= " AND o.order_id < " . (int)$p;
        }

        $sql = "
                SELECT  
                    o.order_id,
                    o.order_no,
                    o.customer_id, 
                    o.telephone, 
                    o.date_added,
                    TRIM(LOWER(o.payment_code)) as payment_code,
                    o.firstname, 
                    o.lastname, 
                    o.shipping_company, 
                    o.shipping_city, 
                    o.shipping_postcode, 
                    o.shipping_zone_id, 
                    o.total, 
                    o.currency_code, 
                    o.currency_value, 
                    o.live_currency_conversion_rate, 
                    o.stock_transfer, 
                    o.operations_status, 
                    o.store_voucher, 
                    o.self_order, 
                    o.order_from, 
                    o.store_name, 
                    o.comment, 
                    o.code_version
                FROM `" . DB_PREFIX . "order` o  
                " . $sub_join . $join .
                " WHERE o.store_id IN (" . WSB_STORES_ID . ")  
                        AND o.franchise_id = 0 
                ". $whr;

        $sql .= " GROUP BY o.order_id ORDER BY o.order_id DESC ";

        if (isset($data['limit'])) {
            if ( (int)$data['limit'] < 1) {
                $data['limit'] = 15;
            }

            $sql .= " LIMIT " . (int) $data['limit'];
        }

        $query = $this->db->query($sql);
        $result['data'] = $query->rows;
        return $result;
    }

    //get gcm_reg_id, vikas ,2017
    public function getGcmRegId($customer_id) {
        // Getting gcm_registration_id to check if app is installed or not
        $sql = "SELECT ws_gcm_registration_id AS gcm_reg_id
                FROM " . DB_PREFIX . "customer
                WHERE customer_id = '" . (int) $customer_id . "'";
        $gcm_query = $this->db->query($sql);
        $result = !empty($gcm_query->row['gcm_reg_id']) ? true : false;
        return $result;
    }

    //get order status name
    public function getOrderStatusName($order_status_id) {
        // Getting order status in english language
        $sql = "SELECT name FROM " . DB_PREFIX . "order_status
                WHERE order_status_id = '" . (int) $order_status_id . "'
                  AND language_id = 1";
        $status_query = $this->db->query($sql);
        $result = $status_query->num_rows ? $status_query->row['name'] : '';
        return $result;
    }

    /** Works only for code_version = 1.0 */
    public function getAdvanceCollected($order_id) {
        $query = $this->db->query("SELECT sum(value) as value
                                   FROM " . DB_PREFIX . "order_total
                                   WHERE order_id = '" . (int) $order_id . "'
                                     AND code = 'advance'");

        return $query;
    }

    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT oop.*, op.image FROM " . DB_PREFIX . "order_product oop
		                            LEFT JOIN " . DB_PREFIX . "product op ON op.product_id = oop.product_id
		                           WHERE oop.order_id = '" . (int) $order_id . "' ORDER BY oop.order_product_id ASC");

        return $query->rows;
    }

    public function getOrderOption($order_id, $order_option_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_option_id = '" . (int) $order_option_id . "'");

        return $query->row;
    }

    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product_id . "'");

        return $query->rows;
    }

    public function getOrderVouchers($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");

        return $query->rows;
    }

    public function getOrderVoucherByVoucherId($voucher_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int) $voucher_id . "'");

        return $query->row;
    }

    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order");

        return $query->rows;
    }

    /**
     * @param $canceled - when true will also count canceled orders.
     */
    public function getTotalOrders($data = array(), $canceled = true) {

        $sql = "SELECT COUNT(DISTINCT o.order_id) AS total_orders 
                FROM `" . DB_PREFIX . "order` o
                INNER JOIN `" . DB_PREFIX . "suborder` osub
                  ON osub.order_id = o.order_id 
                WHERE 1 = 1 ";

        $filter_order_status = trim($data['filter_order_status'] ?? '');

        if (!empty($filter_order_status)) {
            $sql .= " AND osub.order_status_id IN (" . $filter_order_status . ") ";
        } else {
            $sql .= " AND osub.order_status_id > 0 ";
        }

        //IF page request is coming from franchise order then IF part will run otherwise else in normal sale page   
        if (isset($data['request_page']) && $data['request_page'] == 'franchise_order') {

            if (!empty($data['filter_franchise_id'])) {

                $sql .= " AND o.franchise_id = '" . $this->db->escape(trim($data['filter_franchise_id'])) . "'";
            } else {

                $sql .= " AND o.franchise_id > 0 ";
            }
        } else {
            $sql .= " AND o.franchise_id = 0  ";
        }

        if (!empty($data['filter_order_no'])) {
            $sql .= " AND o.order_no LIKE '%" . (float) $data['filter_order_no'] . "%'";
        }

        if (!empty($data['filter_city'])) {
            $sql .= " AND o.shipping_city LIKE '%" . $this->db->escape($data['filter_city']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND o.shipping_company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND (CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape(trim($data['filter_customer'])) . "%'
							OR o.email LIKE '%" . $this->db->escape(trim($data['filter_customer'])) . "%'
							OR o.telephone LIKE '%" . $this->db->escape(trim($data['filter_customer'])) . "%'
						)";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(osub.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(osub.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if (isset($data['filter_total_low'])) {
            $sql .= " AND osub.total >= '" . (float) $data['filter_total_low'] . "'";
        }

        if (isset($data['filter_total_high'])) {
            $sql .= " AND osub.total <= '" . (float) $data['filter_total_high'] . "'";
        }

        if (isset($data['filter_low'])) {
            $sql .= " AND o.total >= '" . (float) $data['filter_low'] . "'";
        }

        if (isset($data['filter_high'])) {
            $sql .= " AND o.total <= '" . (float) $data['filter_high'] . "'";
        }

        if (isset($data['filter_store_list']) && !is_null($data['filter_store_list'])) {
            $sql .= " AND o.store_id = '" . (int) $data['filter_store_list'] . "'";
        } else {
            $sql .= " AND o.store_id IN (" . WSB_STORES_ID . ") ";
        }

        if (!empty($data['filter_customer_id'])) {
            $sql .= " AND o.customer_id IN ( " . $data['filter_customer_id'] . ")";
        }

        if (!empty($data['filter_pickup_city_code'])) {
            $sql .= " AND osub.suborder_id LIKE '%" . $this->db->escape($data['filter_pickup_city_code']) . "%'";
        }

        if (!empty($data['branch_code'])) {
            $sql .= " AND osub.suborder_id LIKE '%" . $this->db->escape($data['branch_code']) . "%'";
        }

        if (!empty($data['filter_tracking_no'])) {
            $sql .= " AND osub.tracking_no LIKE '%" . $this->db->escape($data['filter_tracking_no']) . "%'";
        }

        if (!empty($data['order_pickup'])) {
            $sql .= " AND osub.order_status_id IN (" . $data['order_pickup'] . ")";
        }

        if (!empty($data['order_transit'])) {
            $sql .= " AND osub.order_status_id IN (" . $data['order_transit'] . ")";
        }

        if (!empty($data['order_tantative'])) {
            $sql .= " AND osub.order_status_id IN (" . $data['order_tantative'] . ")";
        }

        if (!empty($data['filter_payment_code'])) {
            $sql .= " AND o.payment_code LIKE '" . $this->db->escape($data['filter_payment_code']) . "'";
        }

        if (!empty($data['filter_gst_number'])) {
            $sql .= " AND o.gst_number LIKE '" . $this->db->escape(trim($data['filter_gst_number'])) . "'";
        }

        if (!empty($data['filter_good_process'])) {
            $sql .= " AND o.operations_status = '" . $this->db->escape($data['filter_good_process']) . "'";
        }

        $query = $this->db->query($sql);

        return (int)($query->row['total_orders'] ?? 0);
    }

    public function getTotalOrdersByStoreId($store_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int) $store_id . "' AND franchise_id = 0 ");

        return $query->row['total'];
    }

    public function getTotalOrdersByOrderStatusId($order_status_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int) $order_status_id . "' AND order_status_id > '0'  AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

        return $query->row['total'];
    }

    public function getTotalOrdersByProcessingStatus() {
        $implode = array();

        $order_statuses = $this->config->get('config_processing_status');

        foreach ($order_statuses as $order_status_id) {
            $implode[] = "order_status_id = '" . (int) $order_status_id . "'";
        }

        if ($implode) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . " AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

            return $query->row['total'];
        } else {
            return 0;
        }
    }

    public function getTotalOrdersByCompleteStatus() {
        $implode = array();

        $order_statuses = $this->config->get('config_complete_status');

        foreach ($order_statuses as $order_status_id) {
            $implode[] = "order_status_id = '" . (int) $order_status_id . "'";
        }

        if ($implode) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "  AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

            return $query->row['total'];
        } else {
            return 0;
        }
    }

    public function getTotalOrdersByLanguageId($language_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int) $language_id . "' AND order_status_id > '0'  AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

        return $query->row['total'];
    }

    public function getTotalOrdersByCurrencyId($currency_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int) $currency_id . "' AND order_status_id > '0'  AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

        return $query->row['total'];
    }

    public function createByerInvoiceNo($order_id, $suborder_id, $payment_code) {

        $error_arr = $this->validateBuyerInvoiceId($order_id, $suborder_id, true);
        
        if (!empty($error_arr)) {
            return array('error' => $error_arr);
        } else {

            //This code is written by Nilesh as per new requirement of output_tax_rate changes as per new price (price_per_piece - discount_per_piece)
            $tax = new Tax($this->registry);
            $selector = array(
                'order' => array('order_no','customer_id','total'),
                'suborder' => array('select' => array('total','order_status_id')),
                'order_product' => array(
                    'select'=> array(
                        'hsn_code',
                        'price_per_piece',
                        'discount_per_piece',
                        'output_tax_rates',
                        'order_product_id',
                        'product_id'
                    )
                )
            );
            $order_info  = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
            $customer_id = $order_info['order']['customer_id'] ?? 0;
            $order_no    = $order_info['order']['order_no'] ?? 0;
           
            if ($payment_code == 'credit') {
                // Devendra July 2018, if order is placed on neogrowth, then need to create entry in payment 
                $credit_payment = new CreditPayment($this);
                // this method will check first if order successfully placed on neogrowth or not.
                // if not, then it will do nothing.
                // if yes, then create entry in order payment and advance vouchers.
                $credit_payment->insertNeoGrowthPaymentDetailsIntoDb($order_id);
            }

            /*if ($payment_code == 'rbl_credit') {
                //Before to generate suborder invoice, call RBL orderpunch api to check
                //that order can be processed through RBL
                //If RBL return DPDCount except(NULL or 0) then suborder should be cancelled
                $suborder_total = $order_info['suborder'][$suborder_id]['total'] ?? 0;
                $order_status_id = $order_info['suborder'][$suborder_id]['order_status_id'] ?? 0;
                
                $rbl_credit = new RblPayment($this);     
                $api_response = $rbl_credit->sendOrderPunchRequestToRBL($customer_id, $order_id, $suborder_id, $suborder_total);

                if(isset($api_response['error'])) {
                    //update order history for RBL error message
                        $input = array();
                        $input['order_id']          = $order_id;
                        $input['suborder_id']       = $suborder_id;
                        $input['comment']           = $api_response['error']['rbl'];
                        $input['notes']             = 'RBL OrderPunchAPI';
                        $input['user']              = $this->user->getUserName()['username'];
                        $input['order_status_id']   = $order_status_id;
                        $update_history_url         =  HTTPS_CATALOG .'index.php?route=api/order/history&order_id='.$order_id.'&suborder_id='.$suborder_id.'&order_status_id='.$order_status_id;
                        $rbl_credit->setOrderHistory( $update_history_url, $input );
                    return $api_response;
                }
            }
            */

            if(!empty($order_info['suborder'][$suborder_id]['order_product'])) {
                $product_info = $order_info['suborder'][$suborder_id]['order_product'];
                
                foreach ($product_info as $products) {
                    $updated_price = (float)$products['price_per_piece'] + (float)$products['discount_per_piece'];
                    //getting mrp of product
                    $sql = "SELECT mrp 
                            FROM " . DB_PREFIX . "product
                            WHERE product_id = '".(int) $products['product_id']."'";
                    $mrp = (float)$this->db->query($sql)->row['mrp'];
                    
                    //getting tax_class_id
                    $tax_class_id = $tax->getTaxClassIdFromHSNCode($products['hsn_code']);
                     //getting output tax rates from the updated_price
                    $output_tax_rates = $tax->getTaxRate($updated_price, $tax_class_id, array(), $mrp);
                    
                    if((float)$output_tax_rates != (float)$products['output_tax_rates']) {
                        $this->_updateOrderProduct($products['order_product_id'], $output_tax_rates);
                    }
                }
                //Update order and suborder total
                OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);
            }
            
            //Nilesh code ends
            

            // Get seller invoice prefix as per current financial year
            $financial_year = '';
            if ((int) (date('m')) <= 3) {
                $financial_year = date('y', strtotime('-1 years')) . date('y') . '-';
            } else {
                $financial_year = date('y') . date('y', strtotime('+1 years')) . '-';
            }
            
            $buyer_invoice = new BuyerInvoice($this);
            try {
                $vat_input_rule_id = $buyer_invoice->getSellerDetails($order_id, $suborder_id);
            } catch (Exception $e) {
                exit($e);
            }
            $advance_vouchers = AdvanceVoucherLib::calculateAdvanceVouchers($this->db, $order_id, $suborder_id);

            if (!empty($advance_vouchers)) {
                foreach ($advance_vouchers as $advance_voucher_id => $values) {
                    AdvanceVoucherLib::updateAdvanceVouchersByVoucherId($this->db, $advance_voucher_id, array('value' => $values['value']));
                }
            }
            
            $vat_input_rule_id = key($vat_input_rule_id);

            $prefix = '';
            $sql = "SELECT invoice_prefix
                FROM " . DB_PREFIX . "vat_input_rules
                WHERE rule_id = '" . (int) $vat_input_rule_id . "' ";
            $result = $this->db->query($sql);
            if ($result->num_rows == 1) {
                $prefix = $result->row['invoice_prefix'];
            } else {
                exit("Unique prefix not found. Please contact Tech.");
            }

            $prefix .= $financial_year;
            try {
                $this->db->query(" START TRANSACTION ");
                $query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no
                                  FROM `" . DB_PREFIX . "suborder`
                                  WHERE invoice_prefix = '" . $this->db->escape($prefix) . "'");
                $query_buyer_invoice = $this->db->query("SELECT MAX(buyer_invoice_id) AS buyer_invoice_id
                                                FROM " . DB_PREFIX . "suborder");

                if (!empty($query->row['invoice_no'])) {
                    $invoice_no = ((int) ($query->row['invoice_no'])) + 1;
                } else {
                    $invoice_no = 1;
                }
                $buyer_invoice_id = 1;
                if (!empty($query_buyer_invoice->row['buyer_invoice_id'])) {
                    $buyer_invoice_id = ((int) ($query_buyer_invoice->row['buyer_invoice_id'])) + 1;
                }

                $resposns = $this->db->query("UPDATE `" . DB_PREFIX . "suborder`
                          SET invoice_no = '" . (int) $invoice_no . "',
                              invoice_date = NOW(),
                              invoice_prefix = '" . $this->db->escape($prefix) . "',
                              custom_totals = '',
                              buyer_invoice_id = '" . (int) $buyer_invoice_id . "'
                          WHERE order_id = '" . (int) $order_id . "'
                            AND suborder_id = '" . $this->db->escape($suborder_id) . "'");


                $resposns = $this->db->query("UPDATE `" . DB_PREFIX . "order_product`
                                    SET buyer_invoice_id = '" . (int) $buyer_invoice_id . "'
                                    WHERE order_id = '" . (int) $order_id . "' AND
                                          suborder_id = '" . $this->db->escape($suborder_id) . "' AND
                                          edit_type IN ('SELLER_APPROVED', 'SELLER_PARTIAL') AND
                                          seller_invoice_id > 0 AND 
                                          pickup_status = 'Received' AND
                                          (buyer_invoice_id IS NULL OR buyer_invoice_id=0)

                                ");
                $this->db->query(" COMMIT ");
            } catch (Exception $e) {
                $this->db->query(" ROLLBACK ");
                $resposns = 0;
                echo $e->getMessage();
            }

            return array('invoice_no' => $prefix . $invoice_no);
        }
    }

    /**
     * 
     * @param type $order_product_id
     * @param type $output_tax_rates
     * @author Nilesh, 2018
     */
    private function _updateOrderProduct(int $order_product_id, float $output_tax_rates) : void {
        $sql = "UPDATE " . DB_PREFIX . "order_product
                SET output_tax_rates = '".(float) $output_tax_rates."'
                WHERE order_product_id = '".(int) $order_product_id."'";
        $this->db->query($sql);
    }
    

    public function validateBuyerInvoiceId($order_id, $suborder_id, $generate_invoice = false) {
       
       //Get order, suborder and order product details for check buyer_invoice and store pickup order
        $selector = array('order' => array('select' => 
            array('order_id', 
                'operations_status',
                'stock_transfer',
                'gst_number',
                'payment_zone_id',
                'customer_id',
                'payment_zone'
                )
            ),
            'suborder' => array('select' => array(
                    'buyer_invoice_id',
                    'shipping_method')
            ),
            'order_product' => array('select' => array(
                    'order_product_id',
                    'product_id',
                    'buyer_invoice_id',
                    'seller_id',
                    'seller_invoice_id',
                    'store_sales',
                    'store_pickup',
                    'edit_type',
                    'pickup_status',
                    'model'
                )
            )
        );

        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        $suborder_info   = $order_info['suborder'][$suborder_id];
        $order_product   = $suborder_info['order_product'];
        $gst_number      = $order_info['order']['gst_number'] ?? '';
        $gst_number      = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number      = trim($gst_number);
        $payment_zone_id = $order_info['order']['payment_zone_id'] ?? 0;
        $payment_zone    = $order_info['order']['payment_zone'] ?? '';
        $customer_id     = $order_info['order']['customer_id'] ?? 0;
        $gst_state_code  = 0;

        // Getting Seller Details
        $seller_id_arr = array_unique(array_column($order_product, 'seller_id'));
        $seller_id_imp = implode(',', $seller_id_arr);

        $sql = "SELECT seller_id,
                       nickname, 
                       company,
                       seller_invoice_generate
                    FROM " . DB_PREFIX . "ms_seller 
                    WHERE seller_id IN (" . $seller_id_imp . ")";

        $seller_info = $this->db->query($sql)->rows;
        $seller_info = array_combine(array_column($seller_info, 'seller_id'), $seller_info);

        $seller_arr = array();
        $seller_id_arr_for_generate_invoice = array();
        $seller_id_for_pickup_status = array();
        $buyer_invoice_generate = false;

        foreach ($order_product as $product) 
        {
            $seller_id = $product['seller_id'];
            $seller_array_for_invoice_generate = false;
            
        //first check
           if ( $seller_info[$seller_id]['seller_invoice_generate'] == 0 
                &&
                (
                    $product['edit_type'] == 'YES' || 
                    $product['edit_type'] == 'SELLER_APPROVED' || 
                    $product['edit_type'] == 'SELLER_PARTIAL'
                ) 
            ) {

                if( $product['seller_invoice_id'] == 0 || $product['seller_invoice_id'] == NULL ) 
                {
                    if ( $product['pickup_status'] == 'Received' ) {

                        $seller_array_for_invoice_generate = true;

                        $seller_id_arr_for_generate_invoice[] = $seller_id;

                    } else if ( $product['pickup_status'] != 'Received' ) {
                        
                        $seller_arr['wsb_seller_not_marked_received'][] = array(
                            'model'     => $product['model'],
                            'nickname'  => $seller_info[$seller_id]['nickname'],
                            'company'   => $seller_info[$seller_id]['company']
                        );

                    } 

                } else if( $product['pickup_status'] != 'Received' ) {

                    $seller_id_for_pickup_status[$product['order_product_id']] = $seller_id;
                }

            }

        //second check
            if ( ( 
                    $product['edit_type'] == 'YES' || 
                    $product['edit_type'] == 'SELLER_LATER_DISPATCH'
                ) 
                && !$seller_array_for_invoice_generate 
            ) {
                $seller_arr['seller_not_invoice_generate'][] = array(
                    'model' => $product['model'],
                    'nickname' => $seller_info[$seller_id]['nickname'],
                    'company' => $seller_info[$seller_id]['company']
                );
            }

        //third check
            if (
                (
                    $product['edit_type'] == 'YES' || 
                    $product['edit_type'] == 'SELLER_APPROVED' || 
                    $product['edit_type'] == 'SELLER_PARTIAL'
                ) 
                &&
                $product['pickup_status'] == 'Issue'
            ) {
                $seller_arr['seller_pickup_issue'][] = array(
                    'model' => $product['model'],
                    'nickname' => $seller_info[$seller_id]['nickname'],
                    'company' => $seller_info[$seller_id]['company']
                );
            }

        //fourth check
            if (
                (
                    $product['edit_type'] == 'SELLER_PARTIAL' || 
                    $product['edit_type'] == 'SELLER_APPROVED'
                ) 
                && 
                (
                    (
                        $product['seller_invoice_id'] == 0 || 
                        $product['seller_invoice_id'] == NULL
                    ) 
                    || 
                    $product['pickup_status'] != 'Received' || 
                    $product['buyer_invoice_id'] > 0
                ) 
                && 
                $seller_info[$seller_id]['seller_invoice_generate'] > 0
            ) {
                $seller_arr['seller_not_marked_received'][] = array(
                    'model' => $product['model'],
                    'nickname' => $seller_info[$seller_id]['nickname'],
                    'company' => $seller_info[$seller_id]['company']
                );
            }    

        //fifth check
           if (
                (
                    (
                        $product['edit_type'] == 'SELLER_PARTIAL' 
                        || 
                        $product['edit_type'] == 'SELLER_APPROVED'
                    ) 
                    && 
                    $product['pickup_status'] == 'Received'
                ) 
                || 
                (
                    $seller_info[$seller_id]['seller_invoice_generate'] == 0 
                    && 
                    $product['pickup_status'] == 'Received'
                )
            ) {
                $buyer_invoice_generate = true;
            } 

        }//end of foreach loop


            if ( !$buyer_invoice_generate 
                &&
                 empty($seller_arr['seller_not_marked_received']) 
             ) {
                $seller_arr['buyer_invoice_will_not_generate'] = "<br>There is no product against which invoice can be generated. 
                                                                    It looks like, seller has marked all of them NOT SUPPLIED,
                                                                    or they have been RETURNED BACK TO SELLER.";
            }    


            if ( 
                (  
                 in_array( $this->user->getId(), explode(',', ADMIN_IDS) ) 
                 || 
                 in_array( $this->user->getId(), explode(',', OPERATIONS_ADMIN_IDS) )
                )
                || 
                $order_info['order']['stock_transfer'] == 1
            ) {
                //Do nothing

            } else {

                if ( !empty($order_info['order']['operations_status'] ) 
                    && 
                    $order_info['order']['operations_status'] == 'dont_dispatch'
                ) {
                    $seller_arr['operation_status_issue'] = "<br>Don't Dispatch";
                }
            }

        $this->checking_gstno($seller_arr, $order_info); 

        //Generating seller invoice for seller_invoice_generate=0
        if ( !empty( $seller_id_arr_for_generate_invoice ) 
             && 
             $generate_invoice
         ) {
            
            if ( empty( $seller_arr['wsb_seller_not_marked_received'] ) ) {
            
                    $seller_id_arr_for_generate_invoice = array_unique($seller_id_arr_for_generate_invoice);
                 
                    $seller_invoice = new SellerInvoice($this);
                 
                    foreach ($seller_id_arr_for_generate_invoice as $seller_id) {
                 
                        $seller_invoice->generateInvoiceNo($order_id, $suborder_id, $seller_id, '', '', false);
                 
                    }

              }

            } else if( !empty ( $seller_id_for_pickup_status ) 
                   && 
                   $generate_invoice 
                ) {
                    $product_data = array(
                        'pickup_status' => 'Received',
                        'pickup_last_modified' => date('Y-m-d H:i:s')
                    );
                    foreach ($seller_id_for_pickup_status as $order_product_id => $seller_id) {
                        OrderEdit::updateOrderProduct($this->db, $order_product_id, $product_data);
                    }
                }

        if (!empty($seller_arr)) {

            return $seller_arr;

        } else {

            return false;
        }

    }

    protected function checking_gstno(&$seller_arr, &$order_info)
    {
        $gst_number      = $order_info['order']['gst_number'] ?? '';
        $gst_number      = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number      = trim($gst_number);
        $payment_zone_id = $order_info['order']['payment_zone_id'] ?? 0;
        $payment_zone    = $order_info['order']['payment_zone'] ?? '';
        $customer_id     = $order_info['order']['customer_id'] ?? 0;
        $gst_state_code  = 0;

        if($gst_number != '' && $payment_zone_id > 0) {
            $gst_obj = new GST($this->registry);
            $valid_gst_result = $gst_obj->validateOrderGstNoWithStateCode($gst_number, $payment_zone_id);
            $data = array();
            $this->load->autoLoadLanguage('sale/customer', $data);
            if (!empty(trim($gst_number)) && !($valid_gst_result['result'] === true)) {
                if ($valid_gst_result['message'] == "error_regex") {
                    $seller_arr['gst_no_mismatch'] = $this->language->get('error_gst_number');
                } elseif ($valid_gst_result['message'] == "error_checksum") {
                    $seller_arr['gst_no_mismatch'] = sprintf($this->language->get('error_gst_checksum'), $valid_gst_result['gst_number_details']['gst_number_without_checksum'] . "<b>" . $valid_gst_result['gst_number_details']['gst_number_checksum'] . "</b>");
                } else if ($valid_gst_result['message'] == "error_duplicate") {
                    $user_str = '';
                    if (!empty($valid_gst_result['duplicate_gst_number_details'])) {
                        $duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
                        if (!empty($duplicate_gst_number_customer['telephone'])) {
                            $user_str = 'mobile number <strong>' . $duplicate_gst_number_customer['telephone'] . '</strong>';
                        } else if (!empty($duplicate_gst_number_customer['email'])) {
                            $len = strlen(explode('@', $duplicate_gst_number_customer['email'])[0]);
                            $user_str = 'email <strong>' . $duplicate_gst_number_customer['email'] . '</strong>';
                        }
                        $seller_arr['gst_no_mismatch'] = sprintf($this->language->get('error_exists_gst'), $gst_number, $user_str, '<strong>' . $duplicate_gst_number_customer['customer_id'] . '</strong>');
                    }
                } else if ($valid_gst_result['message'] == "error_gst_no_mismatch") {
                    $sql = "SELECT name as state_name
                            FROM ".DB_PREFIX."zone 
                            WHERE gst_state_code = '".$this->db->escape((int)substr($gst_number, 0, 2))."'";
                    $query = $this->db->query($sql);
                    $state_name = $query->row['state_name'] ?? '';
                    $seller_arr['gst_no_mismatch'] = sprintf($this->language->get('error_gst_no_mismatch'), $gst_number, $state_name, $payment_zone );
                }
            }
        }
    }

    public function cancelInvoiceNo($order_id, $suborder_id) {

        $this->db->query("UPDATE `" . DB_PREFIX . "suborder`
							SET invoice_no = '0',
								invoice_date = NULL
							WHERE order_id = '" . (int) $order_id . "'
								AND suborder_id = '" . $this->db->escape($suborder_id) . "'");
    }

    public function updateOrderProductField($order_product_id, $field, $field_val) {

        $result_query = $this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET " . $field . " = '" . $field_val . "' WHERE order_product_id = " . $order_product_id);
        return $result_query;
    }

    public function updateFormCStatus() {
        $json = array();

        if (isset($this->request->post['order_id'])) {
            $order_id = (int) $this->request->post['order_id'];
        }

        if (isset($this->request->post['cform_submit'])) {
            $cform_submit = $this->request->post['cform_submit'];
        }

        if (isset($this->request->post['refund_status'])) {
            $refund_status = $this->request->post['refund_status'];
        }

        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET cform_submit = '" . $this->db->escape($cform_submit) . "', refund_status = '" . $this->db->escape($refund_status) . "' WHERE order_id = '" . $order_id . "'");

        $json['success'] = sprintf('success');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getOrderHistories($order_id, $suborder_id, $start = 0, $limit = 10) {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query("SELECT oh.date_added,
                                          os.name AS status,
                                          oh.comment,
                                          oh.notify_email,
                                          oh.notify_sms,
                                          oh.notes,
                                          oh.user
                                   FROM " . DB_PREFIX . "order_history oh
                                   LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id
                                   WHERE oh.order_id = '" . (int) $order_id . "'
                                     AND oh.suborder_id = '" . $this->db->escape($suborder_id) . "'
                                     AND os.language_id = 1
                                   ORDER BY oh.date_added ASC LIMIT " . (int) $start . "," . (int) $limit);

        return $query->rows;
    }

    public function getHistoryTime($order_id, $status_id) {
        $query = $this->db->query("SELECT oh.date_added FROM " . DB_PREFIX . "order_history oh WHERE oh.order_id = '" . (int) $order_id . "' AND  oh.order_status_id = '" . (int) $status_id . "' ORDER BY oh.date_added DESC LIMIT 0,1");

        if ($query->row)
            return $query->row['date_added'];
        else
            return false;
    }

    public function getSuborderHistoryTime($order_id, $suborder_id, $status_id) {
        $sql = "SELECT oh.date_added FROM " . DB_PREFIX . "order_history oh
						WHERE oh.order_id = '" . (int) $order_id . "' AND
									oh.suborder_id = '" . $this->db->escape($suborder_id) . "' AND
									oh.order_status_id = '" . (int) $status_id . "'
						ORDER BY oh.date_added DESC LIMIT 0,1";
        $query = $this->db->query($sql);

        if ($query->row)
            return $query->row['date_added'];
        else
            return false;
    }

    public function getTotalOrderHistories($order_id, $suborder_id) {
        $query = $this->db->query("SELECT COUNT(order_history_id) AS total
                                   FROM " . DB_PREFIX . "order_history
                                   WHERE order_id = '" . (int) $order_id . "'
                                     AND suborder_id = '" . $this->db->escape($suborder_id) . "'");

        return $query->row['total'];
    }

    public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int) $order_status_id . "'");

        return $query->row['total'];
    }

    public function getEmailsByProductsOrdered($products, $start, $end) {
        $implode = array();

        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int) $product_id . "'";
        }

        $query = $this->db->query("SELECT DISTINCT email 
            FROM `" . DB_PREFIX . "order` o 
            LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) 
            WHERE (" . implode(" OR ", $implode) . ")  
            AND o.store_id IN (" . WSB_STORES_ID . ")  
            AND o.order_status_id <> '0' 
            AND o.franchise_id = 0   
            LIMIT " . (int) $start . "," . (int) $end);

        return $query->rows;
    }

    public function getTotalEmailsByProductsOrdered($products) {
        $implode = array();

        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int) $product_id . "'";
        }

        $query = $this->db->query("SELECT DISTINCT email 
            FROM `" . DB_PREFIX . "order` o 
            LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) 
            WHERE (" . implode(" OR ", $implode) . ") 
            AND o.store_id IN (" . WSB_STORES_ID . ")  
            AND o.order_status_id <> '0' 
            AND o.franchise_id = 0 
            ");

        return $query->row['total'];
    }

    public function getProductsBySeller($order_id, $suborder_id = '', $seller_id = 0, $edit_type = array(), $order_product_data = array()) {
        $result = array();

        $sql = "SELECT  oop.order_product_id,
                        oop.suborder_id,
                        oop.product_id,
                        oop.model,
                        oop.seller_id,
                        oop.seller_sku,
                        oop.comment,
                        oop.quantity,
                        oop.piece_in_set,
                        oop.quantity*oop.piece_in_set AS total_pieces,
                        oop.transfer_price_per_piece,
                        oop.store_sales,
                        oop.edit_type,
                        oop.pickup_status,
                        oop.sor_product,
                        oop.seller_invoice_id,
                        oop.pickup_last_modified,
                        oop.customer_comment
                FROM " . DB_PREFIX . "order_product oop 
                WHERE oop.order_id = " . (int) $order_id;

        if (!empty($suborder_id)) {
            $sql .= " AND oop.suborder_id = '" . $this->db->escape($suborder_id) . "'";
        }

        if ($seller_id > 0) {
            $sql .= " AND oop.seller_id = " . (int) $seller_id;
        }

        if (!empty($order_product_data)) {
            $sql .= " AND oop.order_product_id IN (" . implode(",", array_column($order_product_data, 'order_product_id')) . ")";
        }


        if (!empty($edit_type)) {
            $sql .= " AND oop.edit_type IN ('" . implode("','", $edit_type) . "')";
        }

        $sql .= " ORDER BY oop.order_product_id ASC";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $result['products'] = $query->rows;

            // Now getting images
            $product_ids = array_column($query->rows, 'product_id');

            $sql = "
                    SELECT
                       product_id, image, expected_dispatch_date
                    FROM 
                       " . DB_PREFIX . "product
                    WHERE 
                       product_id IN (" . implode(',', $product_ids) . ")";

            $image_query = $this->db->query($sql);

            $result['images'] = array();
            $result['store_sales'] = array();
            $result['expected_dispatch_date'] = array();
            foreach ($image_query->rows as $image) {
                $result['images'][$image['product_id']] = $image['image'];
                if(!empty($image['expected_dispatch_date'])){
                    $result['expected_dispatch_date'][$image['product_id']] = date('d M Y', strtotime($image['expected_dispatch_date']) );
                }else{
                    $result['expected_dispatch_date'][$image['product_id']] = '';
                }
            }

            // Now getting seller_details
            $seller_ids = array_unique(array_column($query->rows, 'seller_id'));
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

            $seller_query = $this->db->query($sql);
            $result['sellers'] = array();

            foreach ($seller_query->rows as $seller) {
                $result['sellers'][$seller['seller_id']] = array('nickname' => $seller['nickname'],
                    'company' => $seller['company'],
                    'email' => $seller['email'],
                    'zone_id' => $seller['zone_id'],
                    'pickup_city_code' => $seller['pickup_city_code'],
                    'seller_invoice_generate' => $seller['seller_invoice_generate']
                );
            }
        }
        return $result;
    }

    public function getSellerMailData($order_id, $suborder_id, $seller_id, $order_product_data = array()) {
        if (!empty($order_product_data)) {
            $seller_products = $this->getProductsBySeller($order_id, $suborder_id, $seller_id, '', $order_product_data);
        } else {
            $seller_products = $this->getProductsBySeller($order_id, $suborder_id, $seller_id);
        }

        if (empty($seller_products))
            return false;

        $this->load->model('tool/image');

        $data['order_products'] = array();
        $i = 0;
        $total = 0;
        $data['show_store_sales_notice'] = false;
        $data['show_sor_product_notice'] = false;
        
        $filter_seller_products = $this->filterSellerProductForMail($seller_products['products']);
        $data['seller_product_totals_category_wise'] = array();
        foreach ($filter_seller_products as $key => $value) {
            $i = 0;
            foreach ($value as $product_info) {
                if ($product_info['store_sales'] != 'NO') {
                    $data['show_store_sales_notice'] = true;
                }
                if ($product_info['sor_product'] == '1') {
                    $data['show_sor_product_notice'] = true;
                }
                $data['order_products'][$key][$i]['order_product_id'] = (int)$product_info['order_product_id'];
                $data['order_products'][$key][$i]['seller_invoice_id'] = (int)$product_info['seller_invoice_id'];
                $data['order_products'][$key][$i]['pickup_status'] = $product_info['pickup_status'];
                $data['order_products'][$key][$i]['image'] = $this->model_tool_image->resize($seller_products['images'][$product_info['product_id']], 150, 150);
                $data['order_products'][$key][$i]['sku'] = $product_info['seller_sku'];
                $data['order_products'][$key][$i]['set_description'] = $product_info['comment'];
                $data['order_products'][$key][$i]['quantity'] = $product_info['quantity'];
                $data['order_products'][$key][$i]['total_pieces'] = $product_info['total_pieces'];
                $data['order_products'][$key][$i]['transfer_price_per_piece'] = $this->currency->format($product_info['transfer_price_per_piece']);
                $data['order_products'][$key][$i]['total_amount_row'] = $this->currency->format($product_info['transfer_price_per_piece'] * $product_info['total_pieces']);
                $data['order_products'][$key][$i]['store_sales'] = $product_info['store_sales'];
                $data['order_products'][$key][$i]['edit_type'] = $product_info['edit_type'];
                $data['order_products'][$key][$i]['sor_product'] = $product_info['sor_product'];
                
                
                if (!empty($data['seller_product_totals_category_wise'][$key])) {
                    $data['seller_product_totals_category_wise'][$key] += (float)($product_info['transfer_price_per_piece'] * $product_info['total_pieces']);
                } else {
                    $data['seller_product_totals_category_wise'][$key] = (float)($product_info['transfer_price_per_piece'] * $product_info['total_pieces']);
                }
                $i++;
            }
            $data['seller_product_totals_category_wise'][$key] = $this->currency->format($data['seller_product_totals_category_wise'][$key]);
        }
       
        $selector = array('order' => array('select' => array('order_no', 'store_id', 'store_name', 'store_url')));
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);

        $seller_invoice = new SellerInvoice($this);
        $input_rule_id = $seller_invoice->getInputRuleId($seller_products['sellers'][$seller_id]['zone_id'], $seller_products['sellers'][$seller_id]['pickup_city_code']);
        $buyer_details = $seller_invoice->getBuyerDetails($input_rule_id);
        $data['buyer_details'] = $buyer_details;
        
        $data['store_id']   = $order_info['order']['store_id']; 
        $data['store_name'] = $order_info['order']['store_name'];
        $data['store_url'] = $order_info['order']['store_url'];
        $data['order_no'] = $order_info['order']['order_no'];
        $data['process_date'] = date("F j, Y");
        $data['logo'] = 'http://www.wholesalebox.in/image/' . $this->config->get('config_logo');

        $data['seller_email'] = $seller_products['sellers'][$seller_id]['email'];
        $data['seller_name'] = $seller_products['sellers'][$seller_id]['company'];
        $data['seller_nickname'] = $seller_products['sellers'][$seller_id]['nickname'];
        return $data;
    }

    public function sendSellerMail($order_id, $suborder_id, $seller_id, $order_product_data = array(), $btn_trigger=false) {
        $mail_send_check = $this->checkIfSuborderHasAtleastOneProductForSendMail($order_id, $suborder_id, $seller_id);
        
        if(!$mail_send_check) {
            return false;
        }
        $action_status = 'AUTO';
        if (!empty($order_product_data)) {
            $data = $this->getSellerMailData($order_id, $suborder_id, $seller_id, $order_product_data);
            $data['is_add_remove_item_in_order'] = 1;
            $action_status = 'ADD_UPDATE_PRODUCT';
        } else {
            $data = $this->getSellerMailData($order_id, $suborder_id, $seller_id);
            $data['is_add_remove_item_in_order'] = 0;
            if($btn_trigger) {
                $action_status = 'BTN_TRIGGER';
            }
        }
        
        //For dynamic subject text
        $subject = 'Wholesale Box : Book a new Order No: ' . $suborder_id; 
        $international_subject = 'INTERNATIONAL ORDER - Wholesale Box : Book a new Order No: ' . $suborder_id;
        
        $seller_mail_count = $this->getSellerMailLogCount($order_id, $suborder_id, $seller_id);
        if (!empty($seller_mail_count[$seller_id])) {
            $data['is_add_remove_item_in_order'] = 1;
            $subject = 'Wholesale Box : Update against Order No: ' . $suborder_id;
            $international_subject = 'INTERNATIONAL ORDER - Wholesale Box : Update against Order No: ' . $suborder_id;
        }

        if (empty($data)) {
            return false;
        }
        $data['suborder_id'] = $suborder_id;
        $html = $this->load->view(DIR_ADMIN_TEMPLATE.'mail/seller-order.tpl', $data, true);

        $mail = new PHPMailer();

        $mail->isSMTP();

        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password'); 
        $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
        $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

        $mail->addAddress($data['seller_email'], $data['seller_name']);

        // Get list of additional emails
        $add_emails = $this->db->query("SELECT *
                                        FROM " . DB_PREFIX . "customer_additional_email
                                        WHERE customer_id = '" . (int) $seller_id . "'");

        if ($add_emails->rows) {
            foreach ($add_emails->rows as $row) {
                $mail->addAddress($row['email'], $data['seller_name']);
            }
        }


        // if store sales
        if ($data['show_store_sales_notice']) {
            $mail->addCC(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        }

        // If seller from Jaipur
        $seller_nick_name = strtoupper(substr(trim($data['seller_nickname']), -2));
        $pickup_email_ids = unserialize(PICKUP_EMAIL_IDS);

        if (!empty($pickup_email_ids[$seller_nick_name])) {
            foreach ($pickup_email_ids[$seller_nick_name] as $email_ids) {
                $mail->addCC($email_ids['email_id'], $email_ids['name']);
            }
        }

        if($data['store_id'] == INTERNATIONAL_STORE_ID)
        {
          $mail->Subject = $international_subject;
        }
        else
        {
          $mail->Subject = $subject;  
        }
        
        $mail->msgHTML($html);
        
        if($mail->send()) {
            
            if($action_status == 'AUTO') {
                $user_obj = new User($this->registry); 
                $user_id = $user_obj->getId();
            } else {
                $user_id = $this->user->getId();
            }
            
            $data_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'seller_id' => $seller_id,
                'action' => $action_status,
                'user_id' =>  $user_id,
                'comment' => null
            );
            $this->insertSellerMailLog($data_arr);
        }
    }
    
    /**
     * Function checkIfSuborderHasAtleastOneProductForSendMail used for checking suborder has atleast one product for sending mail
     * @param (int) $order_id 
     * @param (varchar) $suborder_id
     * @param (int) $seller_id  
     * @return (bolean) true if succesful else false 
     * @author Nilesh, 2018
     */
    public function checkIfSuborderHasAtleastOneProductForSendMail (int $order_id, string $suborder_id, int $seller_id) : bool {
        $sql = "SELECT order_product_id 
                FROM " . DB_PREFIX . "order_product
                WHERE (edit_type IN ('YES', 'SELLER_PARTIAL', 'SELLER_APPROVED', 'SELLER_LATER_DISPATCH') OR seller_invoice_id > 0) AND
                      order_id = '" . (int) $order_id . "' AND
                      suborder_id = '" . $this->db->escape($suborder_id) . "' AND
                      seller_id = '".(int) $seller_id."' 
                LIMIT 1";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    public function insertSellerMailLog($data) {
        $sql = "INSERT INTO " . DB_PREFIX . "seller_mail_log(
                    order_id, 
                    suborder_id, 
                    seller_id, 
                    action, 
                    date_added,
                    user_id,
                    comment) 
                VALUES (
                    '" . (int) $data['order_id'] . "',
                    '" . $this->db->escape($data['suborder_id']) . "',
                    '" . (int) $data['seller_id'] . "',
                    '" . $this->db->escape($data['action']) . "',
                    '" . date('Y-m-d H:i:s') . "',    
                    " . (!empty($data['user_id']) ? "'" . (int) $data['user_id'] . "'" : "NULL") . ",    
                    " . (!empty($data['comment']) ? "'" . $this->db->escape($data['comment']) . "'" : "NULL") . ")";
        $this->db->query($sql);
    }

    public function getSellerMailLog($order_id, $suborder_id, $seller_id = '', $action = '') {
        $sql = "SELECT * 
                FROM " . DB_PREFIX . "seller_mail_log 
                WHERE order_id ='" . (int) $order_id . "' AND
                      suborder_id ='" . $this->db->escape($suborder_id) . "'";
        if (!empty($seller_id)) {
            $sql .= " AND seller_id ='" . (int) $seller_id . "'";
        }
        if (!empty($action)) {
            $sql .= " AND action ='" . $this->db->escape($action) . "'";
        }
        $sql .= " ORDER BY seller_id, action";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows) {
            foreach ($query->rows as $value) {
                $seller_id_key = $value['seller_id'];
                $trigger_key = $value['action'];
                $result[$seller_id_key][$trigger_key][] = array(
                    'date_added' => date('d-m-Y', strtotime($value['date_added'])),
                    'user_id' => $value['user_id'],
                    'name' => $this->user->getUserName($value['user_id'])['name']
                );
            }
            
        }
        return $result;
    }
    
    public function getSellerMailLogCount($order_id, $suborder_id, $seller_id = '') {
        $sql = "SELECT COUNT(id) as mail_log_count,
                       seller_id
                FROM " . DB_PREFIX . "seller_mail_log 
                WHERE order_id ='" . (int) $order_id . "' AND
                      suborder_id ='" . $this->db->escape($suborder_id) . "'";
        if (!empty($seller_id)) {
            $sql .= " AND seller_id ='" . (int) $seller_id . "'";
        }

        $sql .= " GROUP BY seller_id";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows) {
            foreach ($query->rows as $value) {
                $seller_id_key = $value['seller_id'];
                $result[$seller_id_key] = $value['mail_log_count'];
            }
        }
        return $result;
    }
    
    public function filterSellerProductForMail($seller_products) {
        if (empty($seller_products)) {
            return $seller_products;
        }
        $final_arr = array();
        foreach ($seller_products as $value) {

            if(($value['edit_type'] == 'YES' || $value['edit_type'] == 'SELLER_LATER_DISPATCH')){
                $final_arr['uninvoiced'][] = $value;
            } else if((int)$value['seller_invoice_id'] > 0 && $value['pickup_status'] == 'Not_Given') {
                $final_arr['invoiced_not_given'][] = $value;
            } else if($value['seller_invoice_id'] > 0 && $value['pickup_status'] != 'Not_Given') {
                $final_arr['invoiced_given'][] = $value;
            }
            
        }
        $return_arr = array();
        if(!empty($final_arr['uninvoiced'])) {
            $return_arr['uninvoiced'] = $final_arr['uninvoiced'];
        }
        if(!empty($final_arr['invoiced_not_given'])) {
            $return_arr['invoiced_not_given'] = $final_arr['invoiced_not_given'];
        }
        if(!empty($final_arr['invoiced_given'])) {
            $return_arr['invoiced_given'] = $final_arr['invoiced_given'];
        }
        
        return $return_arr;
    }

    public function isWayBillReqd($order_id) {

        // List of states (zones) where no way bill is required.
        // Andaman and Nicobar (1475)
        // Chandigarh (1480)
        // Chattisgarh (4232)
        // Dadra & Nagar Haveli (1481)
        // Daman & Diu (1482)
        // Goa (1484)
        // Haryana (1486)
        // Himachal Pradesh (1487)
        // Lakshadweep (1491)
        // Maharashtra (1493)
        // Pondicherry / Puducherry (1499)
        // Rajasthan (1501)
        // Tamil Nadu (1503)

        $noWayBillStates = array(1475, 1480, 4232, 1481, 1482, 1484, 1486, 1487, 1491, 1493, 1499, 1501, 1503);

        $order_query = $this->db->query("SELECT payment_zone_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int) $order_id . "' AND store_id IN (" . WSB_STORES_ID . ") AND franchise_id = 0 ");

        if ($order_query->num_rows) {
            $zone_id = (int) $order_query->row['payment_zone_id'];

            if (in_array($zone_id, $noWayBillStates))
                return false;
            else
                return true;
        }

        return true;
    }

    public function getGatiDocket($service_type = 'gati_ltd') {

        $docket_sql = "SELECT docket_no "
                . "FROM " . DB_PREFIX . "gati_dockets "
                . "WHERE type='" . $this->db->escape($service_type) . "' 
                         AND used = 0 ORDER BY id LIMIT 1";
        $docket_query = $this->db->query($docket_sql);

        if ($docket_query->num_rows) {
            return $docket_query->row['docket_no'];
        } else {
            return false;
        }
    }

    public function getGatiPackageNos(int $pkg_no) {
        $docket_sql = "SELECT gd.package_no "
                . "FROM " . DB_PREFIX . "gati_packages gd "
                . "WHERE gd.used = 0 limit " . $pkg_no;
        $docket_query = $this->db->query($docket_sql);
        $packages = array();
        if ($docket_query->num_rows) {
            if (!empty($docket_query->rows)) {
                foreach ($docket_query->rows as $row) {
                    $packages[] = $row['package_no'];
                }
            }
        }
        return $packages;
    }

    public function changeDocketStatus($docket_no, $order_no, $weight, $comments = '', $used = '0', $is_success, $post_data = '', $response = '') {
        $docket_sql = "UPDATE " . DB_PREFIX . "gati_dockets gd
    					SET 
                                            gd.used         = '" . (int) $used . "',
                                            gd.is_success   = '" . (int) $is_success . "',
                                            gd.order_no     = '" . $this->db->escape($order_no) . "',
                                            gd.weight       = '" . (float) $weight . "',
                                            gd.comments     = '" . $this->db->escape($comments) . "',
                                            gd.post_data    = '" . $this->db->escape($post_data) . "',
                                            gd.response_data= '" . $this->db->escape($response) . "'
                                        WHERE gd.docket_no  = " . $this->db->escape($docket_no);

        if ($this->db->query($docket_sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function changeGatiPackageStatus($packages = array()) {

        if (empty($packages))
            return;

        $package_sql = "UPDATE " . DB_PREFIX . "gati_packages gd
    					SET 
                                            gd.used         = '1'
                                        WHERE gd.package_no  IN (" . implode(',', $packages) . ") ";

        if ($this->db->query($package_sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function getSalesStaffList() {

        $sales_staff = array('0' => array('name' => '--None--'));

        $sql = "SELECT 
                    staff_id,
                    role,
                    name,
                    telephone,
                    active_status,
                    date_added,
                    date_modified
                FROM 
                    " . DB_PREFIX . "sales_staff 
                WHERE 
                    user_type = 'DirectSale' 
                    AND active_status = 1 
                ORDER BY 
                    name ASC
              ";
        $staff_query = $this->db->query($sql);

        if ($staff_query->rows) {
            foreach ($staff_query->rows as $staff) {

                $back_date = date("Y-m-d", strtotime("-30 Days"));

                if ($staff['active_status'] != 1 && $staff['date_modified'] < $back_date) {
                    continue;
                }
                $active_status = ($staff['active_status'] == 1) ? 'active' : 'inactive';
                $sales_staff[$staff['staff_id']]['name'] = $staff['role'] . '-' . $staff['name'] . '-' . $staff['telephone'] . '-' . $active_status;
                $sales_staff[$staff['staff_id']]['status'] = $staff['active_status'];
            }
        }

        return $sales_staff;
    }

    /**
     * getOrderTagedSalesStaff
     * Get Sales staff which are taged in an Order by oc_order_sales_staff
     * @param  INTEGER  order_id
     * @return ARRAY    result
     * @author Garvit
     */
    public function getOrderTagedSalesStaff($order_id) {
        $result = $this->db->query("SELECT oss.*, ss.name, ss.active_status, ss.role, ss.telephone FROM " . DB_PREFIX . "order_sales_staff as oss LEFT JOIN  " . DB_PREFIX . "sales_staff as ss 
            ON oss.sales_staff_id = ss.staff_id WHERE order_id = '" . $order_id . "' ")->rows;
        return $result;
    }

    public function getSalesPersonName($staff_id) {

        $staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sales_staff WHERE staff_id = '" . (int) $staff_id . "'");

        if ($staff_query->rows) {
            return $staff_query->row['name'];
        } else {
            return false;
        }
    }


    public function delivered_order_customer($data = array()) {

        $sql = "SELECT o.order_id,o.customer_id, o.order_no, o.shipping_company, o.shipping_city, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int) $this->config->get('config_language_id') . "') AS status, o.order_status_id, o.payment_code, o.shipping_code, o.total, o.currency_code, o.currency_value, oh.date_added
   			FROM `oc_order_history` AS oh
   			INNER JOIN oc_order o ON o.order_id = oh.order_id
   			WHERE (
   				DATEDIFF(NOW(),oh.date_added) > 3 AND
    			o.order_status_id = 15
   			) 
            AND o.store_id IN (" . WSB_STORES_ID . ")
            AND o.franchise_id = 0 
   			GROUP BY oh.order_id";

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int) $data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_order_no'])) {
            $sql .= " AND o.order_no LIKE '%" . (float) $data['filter_order_no'] . "%'";
        }

        if (!empty($data['filter_city'])) {
            $sql .= " AND o.shipping_city LIKE '%" . $this->db->escape($data['filter_city']) . "%'";
        }

        if (!empty($data['filter_company'])) {
            $sql .= " AND o.shipping_company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND o.total = '" . (float) $data['filter_total'] . "'";
        }

        $sort_data = array(
            'o.order_id',
            'o.order_no',
            'o.shipping_city',
            'customer',
            'o.shipping_company',
            'status',
            'o.date_added',
            'o.date_modified',
            'o.total'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.order_no";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function deleteOrderProduct($order_product_id, $order_id) {
        $del_sql = "DELETE FROM " . DB_PREFIX . "order_product WHERE order_product_id= " . $order_product_id;

        if ($this->db->query($del_sql)) {
            $results = $this->db->query("SELECT SUM(total) as op_total ,
									SUM(tax) as op_tax,
									SUM(total_pieces) as op_pieces
									FROM " . DB_PREFIX . "order_product
									WHERE order_id=" . $order_id)->row;

            // print_r($results);
            if ($results) {
                $upd_sql = "UPDATE " . DB_PREFIX . "order_total
							SET value = " . $results['op_total'] . " WHERE
							order_id=" . $order_id . "
							AND code= 'sub_total'";

                // echo $upd_sql;

                if ($this->db->query($upd_sql)) {
                    $value = $this->db->query("SELECT SUM(value) as value FROM
											" . DB_PREFIX . "order_total WHERE
												order_id =" . $order_id . " AND
												code NOT IN ('total')")->row;
                    // print_r($value);

                    if ($value) {
                        return $final_delete = $this->db->query("UPDATE " . DB_PREFIX . "order_total SET
										value=" . $value['value'] . "
										WHERE order_id= " . $order_id . " AND
										code='total'");
                        // echo "string";
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }

    public function get_order_no($data = array()) {
        $sql = "SELECT order_no 
        FROM `oc_order` 
        WHERE order_no LIKE '%" . (float) $data['filter_order_no'] . "%' 
        AND store_id IN (" . WSB_STORES_ID . ") 
        AND franchise_id = 0 ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getReturnReasons() {
        $sql = "SELECT * from oc_return_reason";
        $query = $this->db->query($sql);
        $names = $query->rows;
        return $names;
    }

    public function getReturnActions() {
        $sql = "SELECT * from oc_return_action";
        $query = $this->db->query($sql);
        $names = $query->rows;
        return $names;
    }

    // Function to determine Gati serviceability status and of the pincode
    public function getGatiPincodeInfo($pincode) {
        if ($pincode) {
            $query = $this->db->query("SELECT serviceability, ou, cod FROM " . DB_PREFIX . "gati_pincodes
                                     WHERE pincode = '" . $this->db->escape(preg_replace('/\s+/', '', $pincode)) . "' AND status = 1 ");
            if ($query->num_rows) {
                return $query->row;
            } else
                return false;
        }
    }

    ////again mail send to buyer
    public function resendMailToBuyer($order_id, $suborder_id) {

        $this->load->language('sale/order_mail_to_buyer');
        $data = array();


        $order_data = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, array(
                    'order' => array(),
                    'suborder' => array(),
                    'order_product' => array(),
                    'order_total' => array()
                        )
        );

        $order_info = $order_data['order'];
        $suborder_info = $order_data['suborder'][$suborder_id];

        $buyer_invoice_obj = new BuyerInvoice($this);
        $buyer_invoice_obj->setOrderInfo($order_data);
        $total_data = $buyer_invoice_obj->getTotals($order_id, $suborder_id);
        array_walk($total_data, function( &$total, $key ) {
            $total['code'] = $key;
        });


        $cst = isset($total_data['cst']) ? $total_data['cst']['value'] : '0';
        $refund_c_form = isset($total_data['refund_c_form']) ? $total_data['refund_c_form']['value'] : '0.0';

        $order_info['order_product'] = $suborder_info['order_product'];
        $order_info['order_totals'] = $total_data;
        $this->load->model('checkout/order', 'frontend');
        $this->frontend_model_checkout_order->sendOrderEmail($order_info, $suborder_info, $cst, $refund_c_form, '', '', true);
    }

    // This method gets credit note amount given already determined orderInfo
    // It takes care of the code version to return applied shipping charges for a particular suborder
    // madhur/ vikas ,2017
    public function getCreditNoteAmount($order_info) {

        $credit_note_amount = 0.0;

        // If version 1.0
        if ($order_info['code_version'] == '1.0') {
            $sql = "SELECT SUM(value) AS credit_note_amount
                    FROM " . DB_PREFIX . "order_total
                    WHERE order_id = '" . (int) $order_info['order_id'] . "'
                      AND code = 'credit' ";
            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $credit_note_amount = (float) $query->row['credit_note_amount'];
            }
        } else { // version 2.0
            $sql = "SELECT SUM(amount) AS credit_note_amount
                    FROM " . DB_PREFIX . "customer_transaction
                    WHERE order_id = '" . (int) $order_info['order_id'] . "'";
            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $credit_note_amount = (float) $query->row['credit_note_amount'];
            }
        }

        return $credit_note_amount;
    }

    // This method gets shipping charge given already determined orderInfo and suborderInfo
    // It takes care of the code version to return applied shipping charges for a particular suborder,
    // madhur/ vikas , 2017
    public function getShippingCharge($order_info, $suborder_info) {

        $shipping_charge = 0.0;

        // If version 1.0
        if ($order_info['code_version'] == '1.0') {

            if (!isset($suborder_info['order_total'])) {
                $order_total = OrderInfo::getOrderInfo($this->db, $order_info['order_id'], $suborder_info['suborder_id'], array('order_total' => array()));
                $order_total = $order_total['suborder'][$suborder_info['suborder_id']]['order_total'];
            } else {
                $order_total = $suborder_info['order_total'];
            }

            foreach ($order_total as $total) {
                if ($total['code'] == 'shipping') {
                    $shipping_charge = (float) $total['value'];
                    break;
                }
            }
        } else { // version 2.0
            $shipping_charge = (float) $suborder_info['shipping_charge'];
        }

        return $shipping_charge;
    }

    /**
     * Method to return an today total order by customer_id(s)
     * @param int customer_id
     * Output: number of today total order
     * Author: vikas
     */
    public function getTotalTodayOrders($customer_ids) {
        if (empty($customer_ids))
            return 0;

        $is_single_cid = false;
        $single_cid = 0;

        // if not an array, but instead, a single customer is passed
        if (!is_array($customer_ids)) {
            $is_single_cid = true;
            $single_cid = $customer_ids;

            $customer_ids = array($customer_ids);
        }

        $sql = "SELECT o.customer_id, 
                       COUNT(DISTINCT o.order_id) as total_orders_today
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                  ON (osub.order_id = o.order_id)
        		WHERE DATEDIFF(NOW(),o.date_added) = 0
                  AND o.customer_id IN (" . implode(',', $customer_ids) . ") 
                  AND osub.order_status_id > 0 
                  AND osub.order_status_id != 2  
                  AND o.franchise_id = 0 
                GROUP BY o.customer_id ";
        $query = $this->db->query($sql);

        $total_orders_today = 0;
        if ($query->num_rows) {

            if ($is_single_cid) {
                return (int) $query->row['total_orders_today'];
            }

            // Array input
            $total_orders_today = array(); // reinitializing to array
            $total_orders_today = array_combine(array_column($query->rows, 'customer_id'), array_column($query->rows, 'total_orders_today')
            );
        }
        return $total_orders_today;
    }

    /**
     * Method to return an city_code
     * @param int order_id
     * Output: city code i.e. JP,ST...
     * Author: vikas, 2016
     */
    public function getCityCode($order_id) {
        $sql = "SELECT DISTINCT MID(model,5,2) as city_code FROM oc_order_product WHERE order_id =" . (int) $order_id . " ORDER BY model ASC";
        $city_code = $this->db->query($sql);
        if ($city_code->num_rows > 0) {
            $city_codes = implode(',', array_column($city_code->rows, 'city_code'));
        } else {
            $city_codes = '';
        }
        return $city_codes;
    }

    /**
     * Method for get Order Payment History i.e. how many rupees customer paid , how many rupees sent on link , and how many refund
     * @param: $order_id : Integer of order id
     * @return: total_link_send_amount, paid_by_customer, total_refund_amount
     * @author: garvit, 2016
     * @author: vikas, DEC 2017 (updation)
     */
    public function getOrderPaymentHistory($order_id) {
        $data = array();
        $sql = "SELECT
					payment_id,
					merchant_txn_id,
					order_no,
					txn_status,
					payment_mode,
					amount,
					txn_date_time,
					op.date_added,
					payment_gateway,
					successfull,
					reference,
					payment_link,
					TRIM(CONCAT(u.firstname, ' ', u.lastname)) AS user,
                    bank_transfer_mode
				FROM " . DB_PREFIX . "order_payment AS op
				LEFT JOIN " . DB_PREFIX . "user AS u ON op.user_id = u.user_id 
                WHERE order_id = " . (int)$order_id;

        $result = $this->db->query($sql)->rows;

        $data['result']         = $result;
        $total_link_send_amount = 0;
        $payed_by_customer      = 0;
        $total_refund_amt       = 0;
        $actual_refund_amt      = 0;
        $cashback_coupon_amount = 0;

        foreach ($result as $key => $value) {
            if (
                $value['successfull']        == 1 
                && $value['amount']          > 0 
                && $value['payment_gateway'] != 'cashback'
                && $value['payment_gateway'] != 'coupon'
                && $value['payment_gateway'] != 'wsb_credit'
                && $value['txn_status']      != 'cheque_deposited'
            ) {
                $payed_by_customer += $value['amount'];
            } elseif (
                $value['amount']          < 0) {
                $total_refund_amt += $value['amount'];
            }
            
            if ($value['amount'] >= 0) {
                $total_link_send_amount += $value['amount'];
            }
            if ($value['successfull'] == 1 && ($value['payment_gateway'] == 'cashback' || $value['payment_gateway'] == 'coupon')) {
                $cashback_coupon_amount += $value['amount'];
            }

            if(
                $value['successfull']        == 1 
                && $value['amount']          < 0
                && $value['payment_gateway'] != 'cashback'
                && $value['payment_gateway'] != 'coupon'
                && $value['payment_gateway'] != 'wsb_credit'
            ) {
                $actual_refund_amt += $value['amount'];
            }
        }
        $data['total_link_send_amount'] = $total_link_send_amount;
        $data['payed_by_customer']      = $payed_by_customer;
        $data['total_refund_amt']       = $total_refund_amt;
        $data['actual_refund_amt']      = $actual_refund_amt;
        $data['cashback_coupon_amount'] = $cashback_coupon_amount;

        return $data;
    }

    /**
     * Method to Update Order Total
     * @param int order_id
     * @param int suborder_id
     * Author: vikas, 2016
     */
    private function _updateOrderTotal($order_id, $suborder_id) {

        // Finding New Suborder total
        $query = $this->db->query("SELECT SUM(value) as new_suborder_total
                                   FROM " . DB_PREFIX . "order_total
                                   WHERE order_id ='" . (int) $order_id . "'
                                     AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                                     AND code != 'total'
                                     AND code != 'advance'
                                     AND code != 'round_off'");
        $new_suborder_total = (float) ($query->row['new_suborder_total']);
        $rounded_new_suborder_total = round($new_suborder_total);
        $new_suborder_round_off = $rounded_new_suborder_total - $new_suborder_total;

        //Updating the round_off. Check if the round_off exists
        $query = $this->db->query("SELECT order_total_id FROM " . DB_PREFIX . "order_total
                  WHERE order_id ='" . (int) $order_id . "'
                    AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                    AND code = 'round_off'");
        if ($query->num_rows) {
            // new calculated round_off = 0
            if ($new_suborder_round_off == 0.0) {
                $query = $this->db->query("DELETE FROM " . DB_PREFIX . "order_total
                                           WHERE order_id ='" . (int) $order_id . "'
                                             AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                                             AND code = 'round_off'");
            } else {
                // update the existing value
                $query = $this->db->query("UPDATE " . DB_PREFIX . "order_total
                                           SET value = " . (float) $new_suborder_round_off . "
                                           WHERE order_id ='" . (int) $order_id . "'
                                             AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                                             AND code = 'round_off'");
            }
        } else { // no existing round_off
            if ($new_suborder_round_off == 0.0) {
                // Do nothing
            } else {
                // Insert a new round_off
                $query = $this->db->query("INSERT INTO " . DB_PREFIX . "order_total
                                           SET order_id ='" . (int) $order_id . "',
                                               suborder_id = '" . $this->db->escape($suborder_id) . "',
                                               code = 'round_off',
                                               title = 'Round Off',
                                               value = " . (float) $new_suborder_round_off . ",
                                               sort_order = 8");
            }
        }

        // Updating the suborder total in oc_order_total and oc_suborder
        $query = $this->db->query("UPDATE " . DB_PREFIX . "order_total
                                           SET value = " . (float) $rounded_new_suborder_total . "
                                           WHERE order_id ='" . (int) $order_id . "'
                                             AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                                             AND code = 'total'");
        $query = $this->db->query("UPDATE " . DB_PREFIX . "suborder
                                           SET total = " . (float) $rounded_new_suborder_total . "
                                           WHERE order_id ='" . (int) $order_id . "'
                                             AND suborder_id = '" . $this->db->escape($suborder_id) . "'");


        // Calculate the new Overall Order total and update in oc_order
        $query = $this->db->query("SELECT SUM(value) as new_order_total
                                   FROM " . DB_PREFIX . "order_total
                                   WHERE order_id ='" . (int) $order_id . "'
                                     AND code = 'total'");
        $new_order_total = (float) ($query->row['new_order_total']);
        $query = $this->db->query("UPDATE " . DB_PREFIX . "order
                                   SET total = " . (float) $new_order_total . "
                                     WHERE order_id ='" . (int) $order_id . "'");

        // Return the overall order total
        return $new_order_total;
    }

    /**
     * [getDiscountAdjustmentFactor - This function return Discount Adjustment Factor of and order.
     *  Adjust Factor is ratio of total discount and subtotal.
     * @param  [int] $order_id - Order Id
     * @param  array  $totals - This is the total of an order
     * @return [int] - Return total discount percent of an order
     * @usage $_discount_types -- This is private variable for checking the discount types
     */
    public function getDiscountAdjustmentFactor($order_id, $totals = array()) {
        // if $otal is empty get the order total value from db
        if (empty($totals)) {
            $totals = $this->db->query("Select code,value from oc_order_total where order_id = $order_id")->rows;
        }
        // Getting Subtotal of and order
        if (!empty($totals)) {
            $subtotal = 0;
            foreach ($totals as $key => $total) {
                if ($total['code'] == 'sub_total') {
                    $subtotal = (float) $total['value'];
                    break;
                }
            }
            $discount_total = 0;

            // Getting total discount of an order
            foreach ($totals as $key => $total) {
                if (in_array($total['code'], $this->_discount_types)) {
                    $discount_total += (float) $total['value'];
                }
            }
            $discount_factor = 0;
            // Getting Total percent factor
            if (!empty($subtotal)) {
                $discount_factor = (float) $discount_total / (float) $subtotal;
                return $discount_factor;
            }
        }
    }

    public function getSellerInvoices($order_id, $suborder_id) {
        $sql = "SELECT osi.seller_id, 
                        osi.seller_invoice_prefix, 
                        osi.seller_invoice_no, 
                        osi.date_added, 
                        osi.seller_invoice_id 
                 FROM " . DB_PREFIX . "seller_invoice osi 
                 INNER JOIN " . DB_PREFIX . "order_product oop ON oop.seller_invoice_id = osi.seller_invoice_id 
                 WHERE oop.order_id = '" . (int) $order_id . "'
                   AND oop.suborder_id = '" . $this->db->escape($suborder_id) . "' 
                   AND osi.order_id = '" . (int) $order_id . "'
                   AND osi.suborder_id = '" . $this->db->escape($suborder_id) . "' 
                   AND osi.seller_id = oop.seller_id 
                 GROUP BY osi.seller_invoice_id";
        $result = $this->db->query($sql);
        $rows = array();
        if (!empty($result) && $result->num_rows > 0) {
            // $rows = $result->rows;
            // $rows = array_combine(
            //         array_column($rows, 'seller_id'), $rows
            // );
            array_walk($result->rows, function($values, $key) use(&$rows) {
                $rows[$values['seller_id']][] = $values;
            });
            return $rows;
        }
    }

    /** Method For get if order history has delivered atleast once, cancel invoice button will not show
     * @param : order_id : Integer for order id
     * @param : suborder_id : String for suborder id
     * @output : TURE or FALSE
     * @author : Vikas, 2017
     */
    public function getTotalOrderHistoryOfOrderStatus($order_id, $suborder_id) {
        $sql = "SELECT COUNT(*) as total
                FROM " . DB_PREFIX . "order_history
                WHERE order_id = '" . (int) $order_id . "'
                    AND suborder_id = '" . $this->db->escape($suborder_id) . "'
                    AND (order_status_id = 15
                    OR order_status_id = 5)";
        $query = $this->db->query($sql);

        if ($query->row['total'] > 0) {
            return false;
        } else {
            return true;
        }
    }

    /** Method to check if suborder has any SELLER_LATER_DISPATCH or YES product or not
     * @param : order_id : Integer for order id
     * @param : suborder_id : String for suborder id
     * @output : Returns the array of product which has SELLER_LATER_DISPATCH or YES.
     * @author : Nilesh, 2017
     */
    public function checkProductEditType($order_id, $suborder_id) {

        $result = array();
        $sql = "SELECT order_product_id FROM " . DB_PREFIX . "order_product WHERE edit_type IN ('YES', 'SELLER_LATER_DISPATCH') and order_id='" . (int) $order_id . "' and suborder_id='" . $this->db->escape($suborder_id) . "'";
        $result = $this->db->query($sql)->rows;

        return $result;
    }

    public function getAmountTentativeAdvance($order_id) {

        $sql = "SELECT
                  ota.tentative_advance_id, ota.payment_mode, ota.amount, ota.confirm, ota.transaction_status
                FROM
                  oc_tentative_advance ota
                WHERE
                  ota.order_id = '" . (int) $order_id . "'
                ORDER BY
                  tentative_advance_id DESC
                LIMIT 1";


        $amt_query = $this->db->query($sql);
        if ($amt_query->num_rows) {

            return $amt_query->row;
        }
    }

    /**
     * Get First order data of a customer
     * Used for reporting purpose, specially being used in reporting
     */
    public function getFirstOrderDataOfCustomer($customer_id) {
        $sql = "SELECT o.order_id, o.order_no, o.payment_firstname, o.payment_lastname, o.payment_city,
                        o.date_added, o.total, o.tracking, os.order_status_id
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder os
                ON o.order_id = os.suborder_id
                WHERE os.order_status_id > 0
                 AND os.order_status_id <> 2
                AND o.customer_id = " . $customer_id . "
                ORDER BY o.date_added ASC
                LIMIT 1
                ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function sendOutOfStockMailToCustomer($data) {
        if (!empty($data['order_product_id'])) {
            $buyer_invoice = new BuyerInvoice($this);
            $fields = array(
                'order_product_id',
                'product_id',
                'model',
                'name',
                'price_per_piece',
                'piece_in_set',
                'quantity',
                'discount_per_piece',
                'output_tax_rates'
            );
            $data['order_products'] = $buyer_invoice->getProductArrayByOrderProductIds($data['order_product_id'], $fields);
            $product_ids = array_column($data['order_products'], 'product_id');
            $sql = "SELECT product_id,
                           image
                    FROM  " . DB_PREFIX . "product
                    WHERE product_id IN (" . implode(',', $product_ids) . ")";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                $data['pid_to_img'] = array_combine(
                        array_column($result->rows, 'product_id'), array_column($result->rows, 'image')
                );
            }
            $selector = array(
                'order' => array(
                    'select' => array(
                        'firstname',
                        'lastname',
                        'email',
                        'order_no'
                    )
                )
            );
            $order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], '', $selector)['order'];
            $data = array_merge($data, $order_info);
            $html = MailTemplate::getOutOfStockMail($data);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            ;
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addReplyTo($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
            $mail->addAddress($data['email'], $data['firstname'] . ' ' . $data['lastname']);
            $mail->Subject = 'Wholesalebox - Order No: ' . $data['order_no'] . ' - Some Product(s) are Out of Stock';
            $mail->msgHTML($html);
            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function checkCurrentStockByProductId($product_id, $order_product_id) {
        $sql = "SELECT product_option_value_id
                FROM " . DB_PREFIX . "order_option
                WHERE order_product_id = '" . (int) $order_product_id . "'";
        $op_option = $this->db->query($sql);
        if ($op_option->num_rows > 0) {
            $product_option_value_id = $op_option->row['product_option_value_id'];
            $sql = "SELECT quantity
                    FROM " . DB_PREFIX . "product_option_value
                    WHERE product_option_value_id = '" . (int) $product_option_value_id . "' ";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                return $result->row;
            }
        } else {
            $sql = "SELECT product_id,
                           quantity
                    FROM " . DB_PREFIX . "product
                    WHERE product_id = '" . (int) $product_id . "' ";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                return $result->row;
            }
        }
    }

    public function checkProductLastChangeLog($product_id) {
        $sql = "SELECT new_value,
                       user_id,
                       name,
                       date_added
                FROM " . DB_PREFIX . "admin_change_log 
                WHERE table_id = '" . (int) $product_id . "'  
                AND field_name = 'quantity' 
                AND table_name='oc_product' 
                ORDER BY table_id, date_added DESC 
                LIMIT 1 OFFSET 0";
        $admin_log = $this->db->query($sql);
        $sql = "SELECT new,
                       seller_id,
                       nickname,
                       modified as date_added
                FROM " . DB_PREFIX . "seller_change_log
                WHERE product_id = '" . (int) $product_id . "' AND
                      updated_type LIKE '%quantity%'
                ORDER BY product_id, date_added DESC
                LIMIT 1 OFFSET 0";
        $seller_log = $this->db->query($sql);
        $check_for_seller = true;
        if ($admin_log->num_rows > 0 && $seller_log->num_rows > 0) {
            if (( $admin_log->row['new_value'] == 0 || strpos('= 0 qty.', $admin_log->row['new_value'])) &&
                    strtotime($admin_log->row['date_added']) > strtotime($seller_log->row['date_added'])) {

                return array('edit_by' => 'admin', 'user' => $admin_log->row['name'], 'date_added' => $admin_log->row['date_added']);
            } else if ($seller_log->row['new'] == 0 || strpos('= 0 qty.', $seller_log->row['new']) &&
                    strtotime($admin_log->row['date_added']) < strtotime($seller_log->row['date_added'])) {
                return array('edit_by' => 'seller', 'user' => $seller_log->row['nickname'], 'date_added' => $seller_log->row['date_added']);
            }
        } else if ($seller_log->num_rows > 0) {
            if ($seller_log->row['new'] == 0 || strpos('= 0 qty.', $seller_log->row['new'])) {
                return array('edit_by' => 'seller', 'user' => $seller_log->row['nickname'], 'date_added' => $seller_log->row['date_added']);
            }
        } else if ($admin_log->num_rows > 0) {
            if ($admin_log->row['new_value'] == 0 || strpos('= 0 qty.', $admin_log->row['new_value'])) {

                return array('edit_by' => 'admin', 'user' => $admin_log->row['name'], 'date_added' => $admin_log->row['date_added']);
            }
        }
    }

    /**
     * Method for get All Distnict payment code from order table
     * @return array of payment codes
     * @author vikas, 2017
     */
    public function getAllPaymentCodes() {
        $sql = "SELECT DISTINCT(payment_code) FROM " . DB_PREFIX . "order WHERE 1 AND franchise_id = 0 ";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return array_filter(array_column($query->rows, 'payment_code'));
        }
        return array();
    }

    /**
     * public function to get order_option by multiple order_product_ids
     * @param: $order_product_ids String
     * @return: array
     * @author: Nishu, August 2017
     */
    public function getOrderOptionByOOPIds($order_product_ids) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option 
            WHERE order_product_id IN (" . $this->db->escape($order_product_ids) . ")");
        return $query->rows;
    }

    /**
     * Method for Check all product edit type that is sellers of that suborder has marked something for their products
     * @return true or false
     * @author Nilesh, 2017
     */
    public function checkProductEditTypeForBreakSuborder($order_id, $suborder_id) {
        $sql = "SELECT count(oop.order_product_id) as check_edit_type 
                FROM " . DB_PREFIX . "order_product oop 
                WHERE oop.order_id='" . (int) $order_id . "' AND 
                      oop.suborder_id='" . $this->db->escape($suborder_id) . "' AND 
                      edit_type='YES' AND sor_product=0 
                GROUP BY oop.suborder_id ";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            if ($query->row['check_edit_type'] > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Method for get All Tentative Advance History of perticular order No
     * @param : order_id : Integer of order id
     * @return array of records
     * @author vikas, 2017
     */
    public function getTentativeAdvanceHistory($order_id) {
        $sql = "SELECT  ota.payment_mode,
                        ota.cheque_no,
                        ota.txn_id,
                        DATE(ota.dated) as dated,
                        date_created,
                        ota.amount,
                        ota.notes,
                        ota.branch_name,
                        ota.staff_id,
                        ota.transaction_status,
                        ota.order_payment_id,
                        ota.confirm
                FROM " . DB_PREFIX . "tentative_advance ota
                WHERE ota.order_id = " . (int) $order_id;
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $results = array();
            foreach ($query->rows as $key => $value) {
                $results[$key] = $value;
                $results[$key]['staff_name'] = SalesStaff::getStaffInfoByStaffId($this->db, $value['staff_id'])['name'];
            }
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Method for get Only Tentative Amount of perticular order no.
     * @param : order_id : Integer of order id
     * @return array of records
     * @author vikas, 2017
     */
    public function getOnlyTentativeAmount($order_id) {
        $sql = "SELECT  ota.order_id,
                        IF(
                        SUM(IF(osub.order_status_id = 1,1,0)) > 0,
                        IF(TRIM(LOWER(o.payment_code)) = 'cod',
                           IF(((SUM(IF(ota.confirm = 0 AND (
                                                           (ota.transaction_status IN ('will_deposit', 'deposited') AND ota.payment_mode = 'cash') 
                                                             OR 
                                                           (ota.transaction_status = 'deposited' AND ota.payment_mode = 'cheque') 
                                                          ) 
                                                      AND (ota.order_payment_id IS NULL OR ota.order_payment_id = 0),ota.amount,0))/COUNT(DISTINCT osub.suborder_id))) >= 0.1*o.total,
                              ota.amount,
                              0),
                           IF(((SUM(IF(ota.confirm = 0 AND (
                                                            (ota.transaction_status IN ('will_deposit', 'deposited') AND ota.payment_mode = 'cash') 
                                                              OR 
                                                            (ota.transaction_status = 'deposited' AND ota.payment_mode = 'cheque') 
                                                           ) 
                                                       AND (ota.order_payment_id IS NULL OR ota.order_payment_id = 0),ota.amount,0))/COUNT(DISTINCT osub.suborder_id))
                                                            -
                               o.total) >= -1,
                              ota.amount,
                              0)
                          ),
                          0
                       ) AS tentative_amt
                FROM " . DB_PREFIX . "tentative_advance ota
                INNER JOIN  " . DB_PREFIX . "order_payment opy
                    ON opy.order_id = ota.order_id
                INNER JOIN  " . DB_PREFIX . "suborder osub
                    ON osub.order_id = ota.order_id
                INNER JOIN " . DB_PREFIX . "order o
                    ON o.order_id = ota.order_id
                WHERE ota.order_id = " . (int) $order_id;
        $query = $this->db->query($sql);
        if ($query->row['tentative_amt']) {
            return $query->row['tentative_amt'];
        } else {
            return false;
        }
    }

    //To get the franchise list
    public function getFranchiseList() {
        $sql = "SELECT c.customer_id,
                       CONCAT(c.firstname ,' ', c.lastname) as name,
                       oa.city 
                FROM oc_customer c
                LEFT JOIN oc_address oa
                  ON oa.customer_id = c.customer_id
                WHERE
                    EXISTS(
                            SELECT 1 FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = c.customer_id
                        )
                GROUP BY c.customer_id";

        $query = $this->db->query($sql);        

        $results = array();
        if( $query->num_rows ){
            foreach ($query->rows as $value) {
                $results[$value['customer_id']] = ucwords($value['name']. ' - ' . $value['city']); 
            }
        }

        return $results;
    }

    /**
     * Method for get order processing date
     * @param : $order_id : Integer of order id
     * @return : minimum date of order processing date
     * @author vikas, Dec 2017
     */
    public function getOrderProcessingDate($order_id) {
        $sql = "SELECT MIN(date_added) as date_added
                FROM " . DB_PREFIX . "order_history
                WHERE order_id = '" . (int) $order_id . "' 
                  AND order_status_id IN (9,16) ";
        $query = $this->db->query($sql);
        if ($query->row['date_added']) {
            return $query->row['date_added'];
        } else {
            return false;
        }
    }

    /**
     * getSalesStaffBySearch
     * get sales staff by search 
     * @param : $term
     * @return : array
     * @author : Manish, 22/02/18
     */
    public function getSalesStaffBySearch($term = '') {
        $array = array();
        $back_date = date("Y-m-d", strtotime("-30 Days"));
        $staff_query = $this->db->query("SELECT staff_id, crm_user_id, name, active_status, telephone, role, date_modified  FROM " . DB_PREFIX . "sales_staff WHERE user_type = 'DirectSale' AND name LIKE '%" . $term . "%' ORDER BY name ASC");

        if ($staff_query->rows) {
            foreach ($staff_query->rows as $staff) {
                if ($staff['active_status'] != 1 && $staff['date_modified'] < $back_date) {
                    continue;
                }

                $active_status = ($staff['active_status'] == 1) ? 'active' : 'inactive';
                $name = $staff['role'] . '-' . $staff['name'] . '-' . $staff['telephone'] . '-' . $active_status;
                $array[] = array(
                    'label' => $name,
                    'value' => $name,
                    'id' => $staff['staff_id']
                );
            }
        }

        return $array;
    }


    /**
    * update self order in orders and customer table
    * @param : order_id, customer_id, self_order
    * @return : json
    * @author : Manish, 02-08-18
    */
    public function updateSelfOrder() {
        
        $json = array();

        if (isset($this->request->post['order_id'])) {
            $order_id = (int) $this->request->post['order_id'];
        }

        if (isset($this->request->post['customer_id'])) {
            $customer_id = (int) $this->request->post['customer_id'];
        }

        $self_order = (int) $this->request->post['self_order'];



        $user_id = $this->user->getId();;
        $username = $this->user->getUserName()['username'];
        $comment = 'Self order tag by '.$username;


        if (!empty($order_id)) {

            $get_order = $this->db->query("SELECT self_order FROM oc_order WHERE order_id = ".$order_id);

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET self_order = '" . $this->db->escape($self_order) . "' WHERE order_id = '" . $order_id . "'");
            
            $changes_data = array();
            $changes_data['sort_order']['old_value'] = $get_order->row['self_order'];
            $changes_data['sort_order']['new_value'] = $self_order;

            $product_change_log = new ProductChangeLog($this->registry);
            $product_change_log->recordLogs($order_id, $changes_data, 'self_order', '', 'oc_order', $comment);
        }

        if (!empty($customer_id) && $self_order == 1) {        

            $customer_query = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET self_order = '" . $this->db->escape($self_order) . "' WHERE customer_id = '" . $customer_id . "' and self_order = 0");

            if ($customer_query) {
                $changes_data = array();
                $changes_data['sort_order']['old_value'] = 0;
                $changes_data['sort_order']['new_value'] = 1;
                $product_change_log = new ProductChangeLog($this->registry);
                $product_change_log->recordLogs($customer_id, $changes_data, 'self_order', '', 'oc_customer', $comment);
            }                    
        }

        return $json['success'] = sprintf('Self order tag successfully');
    }

    /**
     * @info:  Public function to get previous order's date with master_id
     * @param:  array $order_ids
     * @return: array
     * @author: Nishu, Dec 2018
    */
    public function getOrderPreviousDate(int $order_id, int $master_id, $order_date) : string{
        $prev_order_date = 'NA';
        $sql = "
                SELECT 
                    c.master_id, 
                    MAX(DATE(o.date_added) ) AS prev_order_date
                FROM
                    ".DB_PREFIX."customer c
                        JOIN
                    ".DB_PREFIX."order o ON o.customer_id = c.customer_id
                        AND o.store_id IN (0 , 2, 9)
                        AND o.franchise_id = 0 
                        JOIN
                    ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
                        AND osub.order_status_id > 0
                        AND osub.order_status_id <> 2
                WHERE
                    (c.master_id = ".(int)$master_id. "
                        AND o.order_id < ". (int)$order_id ."
                        AND o.date_added < DATE('".$order_date."') )
                GROUP BY c.master_id
               ";

        $qry = $this->db->query($sql);
        if($qry->num_rows > 0){

            $prev_order_date = date('d-M-y', strtotime($qry->row['prev_order_date']));

        }

        return $prev_order_date;
    }

    /**
     * @info: Public function to get All CN's total amount for given order_id
     * @param: int $order_id
     * @author: Nishu, Dec 2018
    */
    public function getAllCnTotalByOrderId(int $order_id){
        $sql = "SELECT 
                    COALESCE( SUM(net_refundable), 0) AS cn_amount
                  FROM
                    ".DB_PREFIX."credit_note
                  WHERE
                    order_id = ". (int)$order_id ."
                    AND credit_note_status = 1";

        $qry = $this->db->query($sql);
        $cn_amount = $qry->row['cn_amount'];

        return $cn_amount;
    }

     /**
     * @info: Public function to get DPD Count status for RBL orders
     * @param: int $order_id
     * @author: MSA, July 2019
    */
    public function getOrderRblDPDStatus(int $order_id)
    {
        $dpd_count = 1;
        $sql = "
                SELECT 
                    `dpd_count`
                FROM 
                    ".DB_PREFIX."rbl_api_log
                WHERE
                     order_id = ". (int)$order_id ."
                     AND
                     api_type = 'orderPunch'
                     AND
                     ( api_status = 0 OR api_status IS NULL )
                     AND
                     status = 'SUCCESS'

            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $dpd_count = $result->row['dpd_count'];
        }
        return $dpd_count;
    }

    /**
     * Method for get Order's Payment History i.e. how many rupees customer paid , how many rupees sent on link , and how many refund
     * @param: $order_ids : comma seperated order ids
     * @return: total_link_send_amount, paid_by_customer, total_refund_amount
     * @author: Devendra, 2019
     */
    public function getOrdersPaymentHistory($order_ids) {
        if (empty($order_ids)) return array();
        
        $sql = "SELECT
                    order_id,
					payment_id,
					merchant_txn_id,
					order_no,
					txn_status,
					payment_mode,
					amount,
					txn_date_time,
					op.date_added,
					payment_gateway,
					successfull,
					reference,
					payment_link,
					TRIM(CONCAT(u.firstname, ' ', u.lastname)) AS user,
                    bank_transfer_mode
				FROM " . DB_PREFIX . "order_payment AS op
				LEFT JOIN " . DB_PREFIX . "user AS u ON op.user_id = u.user_id 
                WHERE order_id IN (" . implode(',', $order_ids) . ")";

        $result = $this->db->query($sql)->rows;

        $response = array();

        foreach ($result as $key => $value) {
            if (!isset($response[$value['order_id']])) {
                $response[$value['order_id']] = array(
                    'payed_by_customer' => 0,
                    'total_refund_amt' => 0,
                    'total_link_send_amount' => 0,
                    'cashback_coupon_amount' => 0,
                    'actual_refund_amt' => 0
                );
            }
            $response[$value['order_id']]['result'][] = $value;

            if (
                $value['successfull']        == 1 
                && $value['amount']          > 0 
                && $value['payment_gateway'] != 'cashback'
                && $value['payment_gateway'] != 'coupon'
                && $value['payment_gateway'] != 'wsb_credit'
                && $value['txn_status']      != 'cheque_deposited'
            ) {
                $response[$value['order_id']]['payed_by_customer'] += $value['amount'];
            } elseif (
                $value['amount']          < 0) {
                    $response[$value['order_id']]['total_refund_amt'] += $value['amount'];
            }
            
            if ($value['amount'] >= 0) {
                $response[$value['order_id']]['total_link_send_amount'] += $value['amount'];
            }
            if ($value['successfull'] == 1 && ($value['payment_gateway'] == 'cashback' || $value['payment_gateway'] == 'coupon')) {
                $response[$value['order_id']]['cashback_coupon_amount'] += $value['amount'];
            }

            if(
                $value['successfull']        == 1 
                && $value['amount']          < 0
                && $value['payment_gateway'] != 'cashback'
                && $value['payment_gateway'] != 'coupon'
                && $value['payment_gateway'] != 'wsb_credit'
            ) {
                $response[$value['order_id']]['actual_refund_amt'] += $value['amount'];
            }
        }

        foreach ($order_ids as $order_id) {
            if (!isset($response[$order_id])) {
                $response[$order_id] = array(
                    'payed_by_customer' => 0,
                    'total_refund_amt' => 0,
                    'total_link_send_amount' => 0,
                    'cashback_coupon_amount' => 0,
                    'actual_refund_amt' => 0,
                    'result' => array()
                );
            }
        }

        return $response;
    }

        /**
     * Method for get All Tentative Advance History of given order ids
     * @param : order_ids : array of order ids
     * @return array of records
     * @author Devendra, 2019
     */
    public function getTentativeAdvanceHistoryByOrderIds($order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = array();
        }

        $sql = "SELECT  ota.payment_mode,
                        ota.cheque_no,
                        ota.txn_id,
                        DATE(ota.dated) as dated,
                        date_created,
                        ota.amount,
                        ota.notes,
                        ota.branch_name,
                        ota.staff_id,
                        ota.transaction_status,
                        ota.order_payment_id,
                        ota.confirm,
                        ota.order_id
                FROM " . DB_PREFIX . "tentative_advance ota
                WHERE ota.order_id IN (" . implode(",", $order_ids) . ")";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $results = array();
            foreach ($query->rows as $key => $value) {
                $value['staff_name'] = SalesStaff::getStaffInfoByStaffId($this->db, $value['staff_id'])['name'];
                $response[$value['order_id']][] = $value;
            }
        }
        return $response;
    }

    public function getAmountTentativeAdvanceByOrderIds($order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = array();
        }

        $sql = "SELECT
                  ota.order_id, ota.tentative_advance_id, ota.payment_mode, ota.amount, ota.confirm, ota.transaction_status
                FROM
                  oc_tentative_advance ota
                WHERE
                  ota.order_id IN (" . implode(",", $order_ids) . ")
                ";


        $amt_query = $this->db->query($sql);
        if ($amt_query->num_rows) {
            foreach($amt_query->rows as $row) {
                $response[$row['order_id']] = $row;
            }
        }

        return $response;
    }

    public function getOrderProcessingDateByOrderIds($order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = '';
        }

        $sql = "SELECT MIN(date_added) as date_added, order_id
                FROM " . DB_PREFIX . "order_history
                WHERE order_id IN (" . implode(",", $order_ids) . ") 
                  AND order_status_id IN (9,16) 
                GROUP BY order_id";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            foreach($query->rows as $row) {
                $response[$row['order_id']] = $row['date_added'];
            }
        }

        return $response;
    }

    public function getAppInstalledStatusByCustomerIds($customer_ids) {
        if (empty($customer_ids)) return array();
        // Getting gcm_registration_id to check if app is installed or not
        $sql = "SELECT ws_gcm_registration_id AS gcm_reg_id, customer_id
                FROM " . DB_PREFIX . "customer
                WHERE customer_id IN (" . implode(",", $customer_ids) . ")";

        $query = $this->db->query($sql);
        if ($query->num_rows) {
            foreach($query->rows as $row) {
                $response[$row['customer_id']] = !empty($row['gcm_reg_id']) ? true : false;
            }
        }
        return $response;
    }

    /**
     * Method for get Only Tentative Amount of given order ids
     * @param : order_id : array of order ids
     * @return array of records
     * @author Devendra, 2019
     */
    public function getOnlyTentativeAmountByOrderIds($order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = 0;
        }

        $sql = "SELECT  ota.order_id,
                        IF(
                        SUM(IF(osub.order_status_id = 1,1,0)) > 0,
                        IF(TRIM(LOWER(o.payment_code)) = 'cod',
                           IF(((SUM(IF(ota.confirm = 0 AND (
                                                           (ota.transaction_status IN ('will_deposit', 'deposited') AND ota.payment_mode = 'cash') 
                                                             OR 
                                                           (ota.transaction_status = 'deposited' AND ota.payment_mode = 'cheque') 
                                                          ) 
                                                      AND (ota.order_payment_id IS NULL OR ota.order_payment_id = 0),ota.amount,0))/COUNT(DISTINCT osub.suborder_id))) >= 0.1*o.total,
                              ota.amount,
                              0),
                           IF(((SUM(IF(ota.confirm = 0 AND (
                                                            (ota.transaction_status IN ('will_deposit', 'deposited') AND ota.payment_mode = 'cash') 
                                                              OR 
                                                            (ota.transaction_status = 'deposited' AND ota.payment_mode = 'cheque') 
                                                           ) 
                                                       AND (ota.order_payment_id IS NULL OR ota.order_payment_id = 0),ota.amount,0))/COUNT(DISTINCT osub.suborder_id))
                                                            -
                               o.total) >= -1,
                              ota.amount,
                              0)
                          ),
                          0
                       ) AS tentative_amt
                FROM " . DB_PREFIX . "tentative_advance ota
                INNER JOIN  " . DB_PREFIX . "order_payment opy
                    ON opy.order_id = ota.order_id
                INNER JOIN  " . DB_PREFIX . "suborder osub
                    ON osub.order_id = ota.order_id
                INNER JOIN " . DB_PREFIX . "order o
                    ON o.order_id = ota.order_id
                WHERE ota.order_id IN (" . implode(",", $order_ids) . ")";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            foreach($query->rows as $row) {
                $response[$row['order_id']] = $row['tentative_amt'];
            }
        }

        return $response;
    }

        /**
     * getOrderTagedSalesStaff
     * Get Sales staff which are taged in an Order by oc_order_sales_staff
     * @param  ARRAY  order_ids
     * @return ARRAY    result
     * @author Devendra, Sep 2019
     */
    public function getOrderTagedSalesStaffByOrderIds($order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = array();
        }

        $sql = "SELECT oss.*, ss.name, ss.active_status, ss.role, ss.telephone, order_id 
                FROM " . DB_PREFIX . "order_sales_staff as oss 
                LEFT JOIN  " . DB_PREFIX . "sales_staff as ss ON oss.sales_staff_id = ss.staff_id 
                WHERE order_id IN (" . implode(",", $order_ids) . ") ";

        $result = $this->db->query($sql);
        if($result->num_rows) {
            foreach($result->rows as $row) {
                $response[$row['order_id']][] = $row;
            }
        }

        return $response;
    }

    /**
     * Public method to check wsb credit payment entries for the order id
     * @param  int  order_id
     * @return int    1/0
     * @author MSA, Oct 2019
     */
    public function isOrderHasWsbCreditPaymentEntry(int $order_id):int
    {
        $sql = "
            SELECT 
                1
            FROM 
                " . DB_PREFIX . "order_payment
            WHERE
                order_id = '".(int)$order_id."'
                AND
                payment_gateway = 'wsb_credit'
                AND
                successfull = '1' 
            LIMIT 1
        ";
        $result = $this->db->query($sql);
        if($result->num_rows){
            return 1;
        }
        return 0;
    }


}
